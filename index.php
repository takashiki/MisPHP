<?php
//error_reporting(E_ALL);
define('APP', 'app/');

require_once 'vendor/autoload.php';

$config = require_once APP . 'config.php';

$app = new mis\Mis($config);

$app->route('notFound', function() {
  echo 'rewrite 404';
});

//$app->route('/', function() {echo 'null';});

$app->run();