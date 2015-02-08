<?php
namespace mis\core;

use Exception;
use mis\tpl\Template;
use mis\Config;

class View
{
  public static $tpl;
  
  public static function make($view, $data = array(), $toString = false) {
    if (!is_array($data)) {
      throw new Exception('视图参数传入错误');
    }
    
    if (!self::$tpl instanceof Template) {
      self::$tpl = new Template(Config::get('dirs'));
    }
    
    return self::$tpl->render($view, $data, $toString);
  }
}