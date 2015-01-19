<?php
namespace mis;

class Model
{
  public $pdo;
  
  public $table;
  
  public $query;
  
  public function __construct($table = null) {
    $this->pdo = Database::getConn();
    $this->table = $table;
  }
  
  public function all() {
    $query = $this->pdo->query("select * from " . $this->table);
    
    return $query ? $query->fetchAll(\PDO::FETCH_ASSOC) : false;
  }
}