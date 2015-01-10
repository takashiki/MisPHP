<?php
error_reporting(E_ALL);
require_once 'mis/autoloader.php';

$app = new mis\Mis();

$app->route('/test', function() {echo 'null';});
$app->route('/param(/@ddf)', 'param');
$app->route('/class(/@dd)', 'Ttest->index');
$app->route('/sclass/@param', 'Stest::index');

function param($param) {
  echo $param;
}

class Ttest{
  function index($param = 'sff') {
    echo '1';
    echo $param;
  }
}

class Stest{
  static function index($param) {
    echo $param;
  }
}

class Ntest
{
  function ttest($param = 'sf') {
    echo 'netest';
    echo $param;
  }
}

$app->run();