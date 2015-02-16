<?php
namespace mis\db;

use mis\Mis;
use mis\db\teck\Model as Teck;
use mis\db\connection\ConnectionFactory;

class Capsule
{
  /**
	 * The database manager instance.
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	protected $container;
  
  /**
	 * The database manager instance.
	 *
	 * @var \Illuminate\Database\DatabaseManager
	 */
	protected $manager;
  
  /**
	 * Create a new database capsule manager.
	 *
	 * @param  \Illuminate\Container\Container|null  $container
	 * @return void
	 */
	public function __construct(Mis $container = null) {
		$this->container = $container;

		//$this->setupDefaultConfiguration();

		$this->setupManager();
	}
  
  /**
	 * Build the database manager instance.
	 *
	 * @return void
	 */
	protected function setupManager() {
		$factory = new ConnectionFactory($this->container);

		$this->manager = new DatabaseManager($this->container, $factory);
	}
  
  /**
	 * Bootstrap Teck so it is ready for usage.
	 *
	 * @return void
	 */
	public function bootTeck() {
		Teck::setConnectionResolver($this->manager);
	}
}