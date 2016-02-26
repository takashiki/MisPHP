<?php

require 'vendor/autoload.php';

use \app\controller\HomeController;

$c = new HomeController();
$c->index();
