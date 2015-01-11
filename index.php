<?php
//error_reporting(E_ALL);
define('APP', 'app/');

require_once 'mis/Mis.php';

$config = require_once APP . 'config.php';

$app = new mis\Mis($config);

//$app->route('/', function() {echo 'null';});

$app->run();