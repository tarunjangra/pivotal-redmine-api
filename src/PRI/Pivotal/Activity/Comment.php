<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 08/01/15
 * Time: 00:16
 */

namespace PRI\Pivotal\Activity;


class Comment extends Base
{

  public function __construct($data, &$config) {
    parent::__construct($data, $config);
  }

  public function actionCreate(&$pivotalTracker) {
    $collab_client = new \Redmine\Client($this->config->redmine_url, $this->config->redmine_api);
    $issue_array = array(
      'project_id' => $this->config->redmine_project,
      'notes' => $this->message()
    );
    $issues = $collab_client->api('issue')->all(array(
      'project_id'     => $this->config->redmine_project,
      'cf_3' => $this->storyId()
    ));
    $collab_client->api('issue')->update($issues['issues'][0]['id'] ,$issue_array);
    return $this;
  }

  public function actionUpdate(&$pivotalTracker) {
    return $this;
  }

  public function id() {
    return $this->newValues()['id'];
  }

  public function text() {
    return $this->newValues()['text'];
  }
  public function message() {
    return $this->message;
  }

  public function storyId(){
    return $this->newValues()['story_id'];
  }


  public function projectId() {
    return $this->project['id'];
  }

  public function performedById() {
    return $this->performed_by['id'];
  }
}