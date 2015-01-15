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
    $collab_client = new \Redmine\Client($this->config->redmine_url, $this->config->redmine_api);

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

    $issue_array = array(
      'project_id' => $this->config->redmine_project,
      'tracker' => ucfirst($this->type()),
      'status' => ucfirst($current_state),
      'subject' => substr(htmlentities($this->name(), ENT_QUOTES, 'UTF-8'), 0, 254),
      'description' => htmlentities($this->description(), ENT_QUOTES, 'UTF-8') . "\n\nPivotal Story URL: " . $this->url(),
      'assigned_to' => $pivotalTracker->api('member')->listing(array('member_id' => $this->ownedById()))['username'],
      'custom_fields' => array(
        array('id' => 1, 'name' => 'Stage', 'value' => ucfirst($stage)),
        array('id' => 3, 'name' => 'Pivotal Story Id', 'value' => $this->id())
      ),
      'estimated_hours' => $this->config->hours_map[$this->estimate()],
      'created_on' => $this->createdAt()
    );
    $collab_client->api('issue')->create($issue_array);
    return $this;
  }

  public function actionUpdate(&$pivotalTracker) {
    return $this;
  }

  public function id() {
    return $this->newValues()['id'];
  }

  public function description() {
    return $this->newValues()['description'];
  }

  public function name() {
    return $this->newValues()['name'];
  }

  public function type() {
    return $this->newValues()['story_type'];
  }

  public function currentState() {
    return $this->newValues()['current_state'];
  }

  public function labelIds() {
    return $this->newValues()['label_ids'];
  }

  public function ownerIds() {
    return $this->newValues()['owner_ids'];
  }

  public function projectId() {
    return $this->newValues()['project_id'];
  }

  public function ownedById() {
    return $this->newValues()['owned_by_id'];
  }

  public function createdAt() {
    return $this->newValues()['created_at'];
  }

  public function updatedAt() {
    return $this->newValues()['updated_at'];
  }

  public function url() {
    return 'https://www.pivotaltracker.com/story/show/' . $this->id();
  }

  public function estimate() {
    return $this->newValues()['estimate'];
  }

}