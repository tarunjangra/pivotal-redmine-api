<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:49
 */

namespace PRI\Pivotal\Api;


class Project extends Base
{
  public function __construct(&$client, $project) {
    $this->client = $client;
    $this->project = $project;
  }

  public function listing($options) {
    return $this->process($this->client->get(
        '/projects',
        $options ? (array)$options : null
      )
    );
  }
} 