<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 08/01/15
 * Time: 00:15
 */

namespace PRI\Pivotal\Activity;


class Base
{
  protected $_attributes;
  protected $config;

  public function __construct($data,&$config) {
    $this->_attributes = json_decode($data, true);
    $this->config = $config;
  }

  public function __get($property) {
    return isset($this->_attributes[$property]) ? $this->_attributes[$property] : false;
  }

  public function __isset($property) {
    return isset($this->_attributes[$property]) ? true : false;
  }

  public function __unset($property) {
    unset($this->_attributes[$property]);
  }

  public function debug() {
    print_r($this);
  }

  protected function newValues(){
    return $this->changes[0]['new_values'];
  }

  protected function oldValues(){
    return $this->changes[0]['original_values'];
  }

  public function actionDelete(){
    return false;
  }

} 