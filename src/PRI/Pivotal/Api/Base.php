<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 07/01/15
 * Time: 17:51
 */

namespace PRI\Pivotal\Api;


class Base {

  protected $client = null;
  protected $project = null;

  public function process($json_document){
    return json_decode($json_document,true);
  }

} 