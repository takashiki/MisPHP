<?php 
namespace mis\db\connector;

class MySqlConnector extends Connector implements ConnectorInterface 
{
	/**
	 * Establish a database connection.
	 *
	 * @param  array  $config
	 * @return \PDO
	 */
  public function connect(array $config) 
  {
		$dsn = $this->getDsn($config);

		$options = $this->getOptions($config);

		$connection = $this->createConnection($dsn, $config, $options);

		if (isset($config['socket'])) {
			$connection->exec("use {$config['database']};");
		}

		$collation = $config['collation'];

		$charset = $config['charset'];

		$names = "set names '$charset'".
			( ! is_null($collation) ? " collate '$collation'" : '');

		$connection->prepare($names)->execute();

		if (isset($config['strict']) && $config['strict'])
		{
			$connection->prepare("set session sql_mode='STRICT_ALL_TABLES'")->execute();
		}

		return $connection;
	}
  
  /**
	 * Create a DSN string from a configuration. Chooses socket or host/port based on
	 * the 'unix_socket' config value
	 *
	 * @param  array   $config
	 * @return string
	 */
	protected function getDsn(array $config) 
  {
		return $this->configHasSocket($config) ? $this->getSocketDsn($config) : $this->getHostDsn($config);
	}
  
  /**
	 * Determine if the given configuration array has a UNIX socket value.
	 *
	 * @param  array  $config
	 * @return bool
	 */
	protected function configHasSocket(array $config)	
  {
		return isset($config['socket']) && ! empty($config['socket']);
	}
  
  /**
	 * Get the DSN string for a socket configuration.
	 *
	 * @param  array  $config
	 * @return string
	 */
	protected function getSocketDsn(array $config) 
  {
		extract($config);

		return "mysql:unix_socket={$config['unix_socket']};dbname={$database}";
	}
  
  /**
	 * Get the DSN string for a host / port configuration.
	 *
	 * @param  array  $config
	 * @return string
	 */
	protected function getHostDsn(array $config) 
  {
		extract($config);

		return isset($config['port'])
                        ? "mysql:host={$host};port={$port};dbname={$database}"
                        : "mysql:host={$host};dbname={$database}";
	}

}
