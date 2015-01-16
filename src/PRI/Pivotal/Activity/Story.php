<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 08/01/15
 * Time: 00:34
 */

namespace PRI\Pivotal\Activity;


class Story extends Base
{

  public function __construct($data, &$config) {
    parent::__construct($data, $config);
  }

  public function actionCreate(&$pivotalTracker) {

    if ($this->currentState() == 'unscheduled') {
      $current_state = 'icebox';
    } elseif (preg_match('/started|finished|rejected|delivered/', $this->currentState())) {
      $current_state = 'current';
    } elseif($this->currentState() == 'planned') {
      $current_state = 'backlog';
    } else{
      $current_state = 'icebox';
    }

    if ($this->currentState() == 'planned'){
      $stage = 'started';
    }else{
      $stage = $this->currentState();
    }

    $issue_array['project_id']=$this->config->redmine_project;
    if($this->type()) {
      $issue_array['tracker'] = ucfirst($this->type());
    }

    $issue_array['status']=ucfirst($current_state);

    $issue_array['subject']=substr(htmlentities($this->name(), ENT_QUOTES, 'UTF-8'), 0, 254);

    if($this->description()) {
      $issue_array['description'] = htmlentities($this->description(), ENT_QUOTES, 'UTF-8') . "\n\nPivotal Story URL: " . $this->url();
    }

    $issue_array['author_id']=$pivotalTracker->api('member')->listing(array('member_id' => $this->requestedById()))['username'];


    if($this->ownerId()) {
      $issue_array['assigned_to'] = $pivotalTracker->api('member')->listing(array('member_id' => $this->ownerId()))['username'];
    }
    

    $issue_array['custom_fields']=array(
      array('id' => 1, 'name' => 'Stage', 'value' => ucfirst($stage)),
      array('id' => 3, 'name' => 'Pivotal Story Id', 'value' => $this->id())
    );

    if($this->estimate()) {
      $issue_array['estimated_hours'] = $this->config->hours_map[$this->estimate()];
    }

    file_put_contents('output.txt',print_r($issue_array,true), FILE_APPEND);
    file_put_contents('output.txt',print_r($this, true),FILE_APPEND);

    $collab_client = new \Redmine\Client($this->config->redmine_url, $this->config->redmine_api);
    $collab_client->api('issue')->create($issue_array);
    return $this;
  }

  public function actionUpdate(&$pivotalTracker) {
    return $this;
  }

  public function id() {
    return isset($this->newValues()['id'])?$this->newValues()['id']:false;
  }

  public function description() {
    return isset($this->newValues()['description'])?$this->newValues()['description']:false;
  }

  public function name() {
    return isset($this->newValues()['name'])?$this->newValues()['name']:false;
  }

  public function type() {
    return isset($this->newValues()['story_type'])?$this->newValues()['story_type']:false;
  }

  public function currentState() {
    return isset($this->newValues()['current_state'])?$this->newValues()['current_state']:false;
  }

  public function ownerId() {
    return isset($this->newValues()['owner_ids'][0])?$this->newValues()['owner_ids'][0]:false;
  }

  public function requestedById() {
    return isset($this->newValues()['requested_by_id'])?$this->newValues()['requested_by_id']:false;
  }

  public function url() {
    return 'https://www.pivotaltracker.com/story/show/' . $this->id();
  }

  public function estimate() {
    return isset($this->newValues()['estimate'])?$this->newValues()['estimate']:false;
  }

  public function labels(){
    return count($this->newValues()['labels'])?implode(",",$this->newValues()['labels']):false;
  }

}