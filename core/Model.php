<?php
namespace mis\core;

use mis\db\DatabaseManager;

class Model
{
  public $db;
  
  public $table;
  
  public $query;
  
  public function __construct($table = null) {
    $this->db = DatabaseManager::get();
    $this->table = $table;
  }
  
  public function all() {
    $query = $this->db->query("select * from " . $this->table);
    
    return $query ? $query->fetchAll(\PDO::FETCH_ASSOC) : false;
  }
}