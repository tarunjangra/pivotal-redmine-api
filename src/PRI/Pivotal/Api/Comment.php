<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:49
 */

namespace PRI\Pivotal\Api;


class Comment extends Base
{
  public function __construct(&$client, $project) {
    $this->client = $client;
    $this->project = $project;
  }

  public function listing($options) {
    if(!isset($options['story_id'])){
      throw new \InvalidArgumentException();
    }
    return $this->process(
      $this->client->get(
        sprintf("/projects/%s/stories/%s/comments",$this->project,$options['story_id'])
      )
    );
  }

} 