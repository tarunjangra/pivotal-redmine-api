<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:05
 */

namespace PRI;


class PivotalClient
{
  /**
   * @var string
   */
  private $api_key;

  /**
   * @var int|null Redmine response code, null if request is not still completed
   */
  private $response_code = null;

  /**
   * Base url for the PivotalTracker service api.
   */
  const API_URL = 'https://www.pivotaltracker.com/services/v5';

  /**
   * Name of the context project.
   *
   * @var string
   */
  private $project;

  /**
   * Used client to perform rest operations.
   *
   * @var \pri\Rest\Client
   */
  private $client;

  /**
   * Error strings if json is invalid
   */
  private static $json_errors = array(
    JSON_ERROR_NONE => 'No error has occurred',
    JSON_ERROR_DEPTH => 'The maximum stack depth has been exceeded',
    JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
    JSON_ERROR_SYNTAX => 'Syntax error',
  );

  /**
   * @var array APIs
   */
  private $apis = array();


  /**
   * Usage: apikeyOrUsername can be auth key or username.
   * Password needs to be set if username is given.
   *
   * @param string $api_key
   * @param string $project id
   */
  public function __construct(&$config) {
    $this->config = $config;
    $this->client = new Rest\Client(self::API_URL);
    $this->client->addHeader('Content-type', 'application/json');
    $this->client->addHeader('X-TrackerToken', $this->config->pivotal_api);
  }

  public function api($name) {
    $classes = array(
      'story' => 'Story',
      'project' => 'Project',
      'comment' => 'Comment',
      'member' => 'Member',
      'label' => 'Label',
      'task' => 'Task'
    );
    if (!isset($classes[$name])) {
      throw new \InvalidArgumentException();
    }
    if (isset($this->apis[$name])) {
      return $this->apis[$name];
    }
    $c = '\PRI\Pivotal\Api\\' . $classes[$name];
    $this->apis[$name] = new $c($this->client,$this->config->pivotal_project);

    return $this->apis[$name];
  }

  public function activity($raw_data) {

    $data = json_decode($raw_data,true);
    $activity = explode("_",$data['kind']);

    if (isset($this->apis[$data['kind']])) {
      return $this->apis[$data['kind']];
    }
    $c = '\PRI\Pivotal\Activity\\' . ucfirst($activity[0]);
    $this->apis[$data['kind']] = new $c($raw_data,$this->config);
    return $this->apis[$data['kind']]->{'action'.ucfirst($activity[1])}($this);
  }
} 