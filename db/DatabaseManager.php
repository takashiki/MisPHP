<?php
namespace mis\db;

use PDO;
use mis\Mis;
use mis\db\teck\Model as Teck;
use mis\db\connection\ConnectionFactory;

class DatabaseManager
{
  /**
	 * The application instance.
	 *
	 * @var \mis\Mis
	 */
	protected $app;

	/**
	 * The database connection factory instance.
	 *
	 * @var \mis\db\connection\ConnectionFactory
	 */
	protected $factory;

	/**
	 * The active connection instances.
	 *
	 * @var array
	 */
	protected $connections = array();
  
  /**
	 * Create a new database manager instance.
	 *
	 * @param  \mis\Mis  $app
	 * @param  \mis\db\connection\ConnectionFactory  $factory
	 * @return void
	 */
	public function __construct($app) {
		$this->app = $app;
    $this->setupDefaultConfiguration();
		$this->factory = new ConnectionFactory($app);
	}
  
  /**
	 * Get a database connection instance.
	 *
	 * @param  string  $name
	 * @return \mis\db\connection\Connection
	 */
	public function connection() {
		$config = $this->getConfig();

		$driver = $config['driver'];

		return $this->prepare($this->factory->make($config));
	}
  
  /**
	 * Get database config.
	 *
	 * @return array
	 */  
  protected function getConfig() {
		$config = $this->app->config['db'];

		return $config;
	}
  
  /**
	 * Prepare the database connection instance.
	 *
	 * @param  \mis\db\connection\Connection  $connection
	 * @return \mis\db\connection\Connection
	 */
	protected function prepare($connection) {
		$connection->setFetchMode($this->app->config['db']['fetch']);
    
		return $connection;
	}
  
  /**
   * Setup the default database configuration options.
	 *
	 * @return void
	 */
	protected function setupDefaultConfiguration() {
		$this->app->config['db']['fetch'] = PDO::FETCH_ASSOC;
	}
  
  /**
	 * Bootstrap Teck so it is ready for usage.
	 *
	 * @return void
	 */
	public static function bootTeck(Mis $container = null) {
		$manager = new static($container);
    Teck::setConnectionResolver($manager);
	}
}