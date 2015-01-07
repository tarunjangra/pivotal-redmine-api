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
   * @param string $url
   * @param string $apikeyOrUsername
   * @param string $pass (string or null)
   */
  public function __construct($api_key, $project) {

    $this->client = new PRI\Rest\Client(self::API_URL);
    $this->client->addHeader('Content-type', 'application/json');
    $this->client->addHeader('X-TrackerToken', $api_key);
    $this->project = $project;
  }

  public function api($name) {
    $classes = array(
      'attachment'          => 'Attachment',
      'group'               => 'Group',
      'custom_fields'       => 'CustomField',
      'issue'               => 'Issue',
      'issue_category'      => 'IssueCategory',
      'issue_priority'      => 'IssuePriority',
      'issue_relation'      => 'IssueRelation',
      'issue_status'        => 'IssueStatus',
      'membership'          => 'Membership',
      'news'                => 'News',
      'project'             => 'Project',
      'query'               => 'Query',
      'role'                => 'Role',
      'time_entry'          => 'TimeEntry',
      'time_entry_activity' => 'TimeEntryActivity',
      'tracker'             => 'Tracker',
      'user'                => 'User',
      'version'             => 'Version',
      'wiki'                => 'Wiki',
    );
    if (!isset($classes[$name])) {
      throw new \InvalidArgumentException();
    }
    if (isset($this->apis[$name])) {
      return $this->apis[$name];
    }
    $c = 'Redmine\Api\\'.$classes[$name];
    $this->apis[$name] = new $c($this);

    return $this->apis[$name];
  }



  /**
   * Adds a new story to PivotalTracker and returns the newly created story
   * object.
   *
   * @param array $story
   * @param string $name
   * @param string $description
   * @return object
   */
  public function addStory( array $story  )
  {

    return $this->processResponse(
      $this->client->post(
        "/projects/{$this->project}/stories",
        json_encode( $story )
      )
    );
  }

  /**
   * Adds a new task with <b>$description</b> to the story identified by the
   * given <b>$storyId</b>.
   *
   * @param integer $storyId
   * @param string $description
   * @return \SimpleXMLElement
   */
  public function addTask( $storyId, $description )
  {
    return simplexml_load_string(
      $this->client->post(
        "/projects/{$this->project}/stories/$storyId/tasks",
        json_encode( array( 'description' => $description ) )

      )
    );
  }

  /**
   * Adds the given <b>$labels</b> to the story identified by <b>$story</b>
   * and returns the updated story instance.
   *
   * @param integer $storyId
   * @param array $labels
   * @return object
   */
  public function addLabels( $storyId, array $labels )
  {
    return $this->processResponse(
      $this->client->put(
        "/projects/{$this->project}/stories/$storyId",
        json_encode(  $labels )
      )
    );
  }

  /**
   * Returns all stories for the context project.
   *
   * @param array $filter
   * @return object
   */
  public function getStories( $options = null )
  {
    return $this->processResponse(
      $this->client->get(
        "/projects/{$this->project}/stories",
        $options ? (array) $options : null
      )
    );
  }

  /**
   * Returns a list of projects for the currently authenticated user.
   *
   * @return object
   */
  public function getProjects()
  {
    return $this->processResponse(
      $this->client->get(
        "/projects"
      )
    );

  }

  /**
   * Returns list of comments related to a particular story.
   *
   * @return object
   *
   */

  public function getComments($story_id)
  {
    return $this->processResponse(
      $this->client->get(
        "/projects/{$this->project}/stories/{$story_id}/comments"
      )
    );
  }


  public function getMembers($member_id = null)
  {

    $memberships = $this->processResponse($this->client->get(
      "/projects/{$this->project}/memberships"
    ));

    foreach($memberships as $person_object )
    {
      $p_array[$person_object['person']['id']] = $person_object['person'];
    }

    return ($member_id)?$p_array[$member_id]:$p_array;
  }


  /**
   * Returs json decoded respose in an array instead of std objects
   *
   * $param response std class object
   * @return array
   *
   */

  protected function processResponse($response){
    return json_decode($response,true);
  }
} 