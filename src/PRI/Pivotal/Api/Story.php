<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:49
 */

namespace PRI\Pivotal\Api;


class Story extends Base
{

  public function __construct(&$client, $project) {
    $this->client = $client;
    $this->project = $project;
  }

  public function listing($options) {
    return $this->process($this->client->get(
        sprintf('/projects/%s/stories', $this->project),
        $options ? (array)$options : null
      )
    );
  }

} 