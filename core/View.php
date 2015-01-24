<?php
namespace mis\core;

class View
{
  public static $view;
  
  public static $data;
  
  public static function make($view, $data = array()) {
    if (is_array($data)) {
      extract($data);
    } else {
      throw new Exception('视图参数传入错误');
    }
    
    $viewFile = APP.'view/' . $view . '.php';
    if (is_file($viewFile)) {
      require $viewFile;
    } else {
      throw new Exception('视图文件不存在');
    }
  }
}