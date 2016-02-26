<?php

require '/../vendor/autoload.php';

defined('MIS_ENV') or define('MIS_ENV', 'dev');

$app = new Mis\App();
$app->run();
