<?php 
namespace mis\db\connector;

interface ConnectorInterface 
{
	/**
	 * Establish a database connection.
	 *
	 * @param  array  $config
	 * @return \PDO
	 */
	public function connect(array $config);
}
