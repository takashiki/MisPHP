<?php
namespace Mis;

class Mis {
  protected $controller;
  protected $model;
  private static $_map = array();
  private static $_instance = array();

  static public function run() {
    
    $app = new Mis();
    $app->navUrl();
  }
  
  private function navUrl() {
    if(!empty($_SERVER['PATH_INFO'])) {
      $parray = $_SERVER['PATH_INFO'];
      $url = $_SERVER['REQUEST_URI]'];
      $str = explode('/', trim($parray, '\/'));
      $controller = empty($str[0]) ? 'index' : $str[0] . 'Controller';
      $model = empty($str[1]) ? "index" : $str[1];
    } else {
      $controller = isset($_GET['c']) ? $_GET['c'].'Controller' : 'indexController';
      $model = isset($_GET['a']) ? $_GET['a'] : 'index';
    }

    if(!class_exists($controller)) {
      header("Content-type:text/html;charset=utf-8");
      exit($controller."控制器不存在");
    }

    $this->controller = $controller;
    $this->model = empty($model) ? "index" : $model;
    $controllerClass = new $controller();
    $controllerClass->$model();
  }
}