<?php
namespace mis\db;

use mis\Database;

class DatabaseManager
{
  public static $db;
  
  public static $db_config;
  
  public static function init($config) {
    self::$db_config = $config;
    
    self::$db = new Database(self::$db_config);
  }
  
  public static function get($shared = false) {
    if ($shared === true) {
      return self::$db;
    } else {
      return new Database(self::$db_config);
    }
  }
}