<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:49
 */

namespace PRI\Pivotal\Api;


class Member extends Base
{
  public function __construct(&$client, $project) {
    $this->client = $client;
    $this->project = $project;
  }

  public function listing($options) {
    $memberships = $this->process($this->client->get(
      sprintf("/projects/%s/memberships",$this->project)
    ));
    foreach ($memberships as $person_object) {
      $p_array[$person_object['person']['id']] = $person_object['person'];
    }
    return isset($options['member_id']) ? $p_array[$options['member_id']] : $p_array;
  }
} 