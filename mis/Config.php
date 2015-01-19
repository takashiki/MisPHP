<?php
namespace mis;

class Config
{
  private static $instance;
  
  public $config = array();
  
  private function __construct() {
    $configs = glob(APP . 'config/*.php');
    
    foreach ($configs as $config) {
      $key = substr($config, strrpos($config, '/') + 1, -4);
      $this->config[$key] = require $config;
    }
  }
  
  public static function get($key = null) {
    if ($key === null) {
      if (!(self::$instance instanceof self)) {    
        self::$instance = new self();         
      }
      
      return self::$instance->config;
    } else {
      $config = self::get();
      
      return isset($config[$key]) ? $config[$key] : null;
    }
  }
  
  public static function set($key, $value = null) {
    if(is_array($key)) {
      foreach($key as $k => $v) {
        self::set($k, $v);
      }
    } else {
      $config = self::get();
      
      $config[$key] = $value;
    }
  }
  
  private function __clone() {}

  private function __wakeup() {}
}