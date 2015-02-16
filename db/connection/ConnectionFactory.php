<?php
namespace mis\db\connection;

use PDO;
use mis\db\connection\MySqlConnection;
use mis\db\connector\MySqlConnector;

class ConnectionFactory
{
  /**
	 * Establish a PDO connection based on the configuration.
	 *
	 * @param  array   $config
	 * @param  string  $name
	 * @return \mis\db\connection\Connection
	 */
	public function make(array $config) {
		return $this->createSingleConnection($config);
	}

	/**
	 * Create a single database connection instance.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Database\Connection
	 */
	protected function createSingleConnection(array $config)
	{
		$pdo = $this->createConnector($config)->connect($config);

		return $this->createConnection($config['driver'], $pdo, $config['database'], $config['prefix'], $config);
	}
  
  /**
	 * Create a connector instance based on the configuration.
	 *
	 * @param  array  $config
	 * @return \mis\db\onnector\ConnectorInterface
	 *
	 * @throws \InvalidArgumentException
	 */
	public function createConnector(array $config)
	{
		if ( ! isset($config['driver']))
		{
			throw new Exception("A driver must be specified.");
		}

		switch ($config['driver'])
		{
			case 'mysql':
				return new MySqlConnector;

			case 'pgsql':
				return new PostgresConnector;

			case 'sqlite':
				return new SQLiteConnector;

			case 'sqlsrv':
				return new SqlServerConnector;
		}

		//throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]");
	}

	/**
	 * Create a new connection instance.
	 *
	 * @param  string   $driver
	 * @param  \PDO     $connection
	 * @param  string   $database
	 * @param  string   $prefix
	 * @param  array    $config
	 * @return \Illuminate\Database\Connection
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function createConnection($driver, PDO $connection, $database, $prefix = '', array $config = array())
	{
		switch ($driver)
		{
			case 'mysql':
				return new MySqlConnection($connection, $database, $prefix, $config);

			case 'pgsql':
				return new PostgresConnection($connection, $database, $prefix, $config);

			case 'sqlite':
				return new SQLiteConnection($connection, $database, $prefix, $config);

			case 'sqlsrv':
				return new SqlServerConnection($connection, $database, $prefix, $config);
		}

		//throw new InvalidArgumentException("Unsupported driver [$driver]");
	}
}