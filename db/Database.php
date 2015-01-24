<?php
namespace mis\db;

use PDO;
use Exception;
use PDOException;

class Database
{
  public $pdo;
  
  public $db_type;
  
  public $server;
  
  public $username;
  
  public $password;
  
  public $db_name;
  
  public $port;
  
  public $charset;
  
  public $db_file;
  
  public $socket;
  
  public $option = array();
  
  protected $log = array();
  
  public function __construct($options = null)
	{
		try {
      if (!is_array($options)) {
        throw new \Exception('数据库配置不正确');
      }
      
      foreach ($options as $option => $value) {
        $this->$option = $value;
      }

			if (isset($this->port) && is_int($this->port * 1)) {
				$port = $this->port;
			}

			$type = strtolower($this->db_type);
			$is_port = isset($port);

			switch ($type) {
				case 'mariadb':
					$type = 'mysql';

				case 'mysql':
					if ($this->socket) {
						$dsn = $type . ':unix_socket=' . $this->socket . ';dbname=' . $this->db_name;
					} else {
						$dsn = $type . ':host=' . $this->server . ($is_port ? ';port=' . $port : '') . ';dbname=' . $this->db_name;
					}

					// Make MySQL using standard quoted identifier
					$commands[] = 'SET SQL_MODE=ANSI_QUOTES';
					break;

				case 'pgsql':
					$dsn = $type . ':host=' . $this->server . ($is_port ? ';port=' . $port : '') . ';dbname=' . $this->db_name;
					break;

				case 'sybase':
					$dsn = 'dblib:host=' . $this->server . ($is_port ? ':' . $port : '') . ';dbname=' . $this->db_name;
					break;

				case 'oracle':
					$dbname = $this->server ?
						'//' . $this->server . ($is_port ? ':' . $port : ':1521') . '/' . $this->db_name :
						$this->db_name;

					$dsn = 'oci:dbname=' . $dbname . ($this->charset ? ';charset=' . $this->charset : '');
					break;

				case 'mssql':
					$dsn = strstr(PHP_OS, 'WIN') ?
						'sqlsrv:server=' . $this->server . ($is_port ? ',' . $port : '') . ';database=' . $this->db_name :
						'dblib:host=' . $this->server . ($is_port ? ':' . $port : '') . ';dbname=' . $this->db_name;

					$commands[] = 'SET QUOTED_IDENTIFIER ON';
					break;

				case 'sqlite':
					$dsn = $type . ':' . $this->db_file;
					$this->username = null;
					$this->password = null;
					break;
			}

			if (in_array($type, explode(' ', 'mariadb mysql pgsql sybase mssql')) &&$this->charset) {
				$commands[] = "SET NAMES '" . $this->charset . "'";
			}

			$this->pdo = new PDO($dsn, $this->username, $this->password, $this->option);

			foreach ($commands as $value) {
				$this->pdo->exec($value);
			}
		}
		catch (PDOException $e) {
			throw new Exception($e->getMessage());
		}
	}
  
  public function query($query)
	{
		array_push($this->log, $query);

		return $this->pdo->query($query);
	}
}