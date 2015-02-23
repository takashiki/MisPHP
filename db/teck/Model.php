<?php
namespace mis\db\teck;

use PDO;
use mis\db\connector\MySqlConnector;
use mis\db\query\Builder as QueryBuilder;
use mis\db\DatabaseManager as Resolver;

class Model
{  
  /**
	 * The connection name for the model.
	 *
	 * @var string
	 */
	protected $connection;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table;

  /**
	 * The base query builder instance.
	 *
	 * @var \mis\db\query\Builder
	 */
	protected $query;
  
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * The number of models to return for pagination.
	 *
	 * @var int
	 */
	protected $perPage = 15;

	/**
	 * Indicates if the IDs are auto-incrementing.
	 *
	 * @var bool
	 */
	public $incrementing = true;
  
  /**
	 * The connection resolver instance.
	 *
	 * @var \mis\db\ConnectionResolverInterface
	 */
	protected static $resolver;
  
  /**
	 * Create a new Teck model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
  public function __construct() {
    $conn = $this->getConnection();

		$grammar = $conn->getQueryGrammar();

		$this->query = new QueryBuilder($conn, $grammar);
    
    $this->query->from($this->getTable());
  }

  /**
	 * Get the table associated with the model.
	 *
	 * @return string
	 */
	public function getTable() {
		if (isset($this->table)) return $this->table;

		return strtolower(str_replace('\\', '', class_basename($this)));
	}
  
  /**
	 * Get the database connection for the model.
	 *
	 * @return \mis\db\connection\Connection
	 */
	public function getConnection() {
		return static::resolveConnection($this->connection);
	}
  
  /**
	 * Set the connection resolver instance.
	 *
	 * @param  \mis\db  $resolver
	 * @return void
	 */
	public static function setConnectionResolver(Resolver $resolver) {
		static::$resolver = $resolver;
	}
  
  /**
	 * Resolve a connection instance.
	 *
	 * @param  string  $connection
	 * @return \mis\db\connection\Connection
	 */
	public static function resolveConnection($connection = null) {
		return static::$resolver->connection($connection);
	}
  
  /**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)	{
		$instance = new static;

		return call_user_func_array(array($instance, $method), $parameters);
	}
  
  /**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters) {
		return call_user_func_array(array($this->query, $method), $parameters);
	}
}