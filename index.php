<?php
require 'vendor/autoload.php';

use \mis\App as Mis;
use \Interop\Container\ContainerInterface;

use \app\controller\HomeController;
$c = new HomeController();
$c->index();