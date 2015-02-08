<?php
namespace mis\db\teck;

use PDO;
use mis\db\connector\MySqlConnector;
use mis\db\query\Builder as QueryBuilder;

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
	 * Create a new Teck model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
  public function __construct() {
    
  }
  
  /**
	 * Get a new query builder for the model's table.
	 *
	 * @return \mis\db\teck\Builder
	 */
	public function newQuery() {
		//$builder = $this->newQueryWithoutScopes();

		//return $this->applyGlobalScopes($builder);
    return new QueryBuilder();
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
		$query = $this->newQuery();

		return call_user_func_array(array($query, $method), $parameters);
	}
}