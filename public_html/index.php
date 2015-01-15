<?php
/**
 * Created by PhpStorm.
 * User: tarunjangra
 * Date: 16/01/15
 * Time: 02:10
 */

require __DIR__. '/../vendor/autoload.php';
$config = new \PRI\Config();

$app = new \Slim\Slim();
$app->post('/:token', function ($token) use ($config) {
  if ($token == $config->post_security_token){
    $post_json = file_get_contents('php://input');
    if(!empty($post_json)){
      try {
        $config = new \PRI\Config();
        $pivotalTracker = new \PRI\PivotalClient($config);
        $pivotalTracker->activity($post_json);
      }
      catch(Exception $e){
        file_put_contents(__DIR__.'/../output.txt', '('.date('d-m-Y').'): '.$e->getMessage()."\n",FILE_APPEND);
      }
    }
  }
});
$app->run();

