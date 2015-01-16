<?php
namespace mis;

class Conn
{
  public static $pdo;
  
  public static $db_type;
  
  public static $server;
  
  public static $username;
  
  public static $password;
  
  public static $db_name;
  
  public static $port;
  
  public static $charset;
  
  public static $db_file;
  
  public static $socket;
  
  public static $option = array();
  
  public static function init($options = null)
	{
		try {
      if (!is_array($options)) {
        throw new \Exception('数据库配置不正确');
      }
      
      foreach ($options as $option => $value) {
        self::$$option = $value;
      }

			if (isset(self::$port) && is_int(self::$port * 1)) {
				$port = self::$port;
			}

			$type = strtolower(self::$db_type);
			$is_port = isset($port);

			switch ($type) {
				case 'mariadb':
					$type = 'mysql';

				case 'mysql':
					if (self::$socket) {
						$dsn = $type . ':unix_socket=' . self::$socket . ';dbname=' . self::$db_name;
					} else {
						$dsn = $type . ':host=' . self::$server . ($is_port ? ';port=' . $port : '') . ';dbname=' . self::$db_name;
					}

					// Make MySQL using standard quoted identifier
					$commands[] = 'SET SQL_MODE=ANSI_QUOTES';
					break;

				case 'pgsql':
					$dsn = $type . ':host=' . self::$server . ($is_port ? ';port=' . $port : '') . ';dbname=' . self::$db_name;
					break;

				case 'sybase':
					$dsn = 'dblib:host=' . self::$server . ($is_port ? ':' . $port : '') . ';dbname=' . self::$db_name;
					break;

				case 'oracle':
					$dbname = self::$server ?
						'//' . self::$server . ($is_port ? ':' . $port : ':1521') . '/' . self::$db_name :
						self::$db_name;

					$dsn = 'oci:dbname=' . $dbname . (self::$charset ? ';charset=' . self::$charset : '');
					break;

				case 'mssql':
					$dsn = strstr(PHP_OS, 'WIN') ?
						'sqlsrv:server=' . self::$server . ($is_port ? ',' . $port : '') . ';database=' . self::$db_name :
						'dblib:host=' . self::$server . ($is_port ? ':' . $port : '') . ';dbname=' . self::$db_name;

					$commands[] = 'SET QUOTED_IDENTIFIER ON';
					break;

				case 'sqlite':
					$dsn = $type . ':' . self::$db_file;
					self::$username = null;
					self::$password = null;
					break;
			}

			if (in_array($type, explode(' ', 'mariadb mysql pgsql sybase mssql')) &&self::$charset) {
				$commands[] = "SET NAMES '" . self::$charset . "'";
			}

			self::$pdo = new \PDO($dsn, self::$username, self::$password, self::$option);

			foreach ($commands as $value) {
				self::$pdo->exec($value);
			}
		}
		catch (PDOException $e) {
			throw new \Exception($e->getMessage());
		}
	}
  
  public static function getConn() {
    return self::$pdo;
  }
}