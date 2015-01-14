<?php

namespace PRI;

class Config {

  private $_attributes;

  public function __construct() {
    $this->root_path = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
    $this->_attributes = json_decode(file_get_contents($this->root_path.'config'.DIRECTORY_SEPARATOR.'main.json'), true);
  }

  public function __get($property) {
    return isset($this->_attributes[$property])?$this->_attributes[$property]:false;
  }

  public function __isset($property){
    return isset($this->_attributes[$property])?true:false;
  }

  public function __unset($property){
    unset($this->_attributes[$property]);
  }

  public function __set($property, $value) {
    $this->_attributes[$property] = $value;
  }

}