<?php
namespace mis\db\teck;

use PDO;
use mis\db\connector\MySqlConnector;
use mis\Config;

class Model
{
  public $db;
  
  public $table;
  
  public $query;
  
  public function __construct($table = null) {
    $mc = new MySqlConnector();
    $this->db = $mc->connect(Config::get('db'));
    $this->db->setTable = $table;
  }
  
  public function all() {
    $query = $this->db->query("select * from " . $this->table);
    
    return $query ? $query->fetchAll(PDO::FETCH_ASSOC) : false;
  }
  
  public function 
}