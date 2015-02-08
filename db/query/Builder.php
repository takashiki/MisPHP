<?php
namespace mis\db\query;

class Builder
{
  /**
	 * The database connection instance.
	 *
	 * @var \mis\db\Connection
	 */
	protected $connection;
  
  /**
	 * The columns that should be returned.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Indicates if the query returns distinct results.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * The table which the query is targeting.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * The table joins for the query.
	 *
	 * @var array
	 */
	public $joins;

	/**
	 * The where constraints for the query.
	 *
	 * @var array
	 */
	public $wheres;

	/**
	 * The groupings for the query.
	 *
	 * @var array
	 */
	public $groups;

	/**
	 * The having constraints for the query.
	 *
	 * @var array
	 */
	public $havings;

	/**
	 * The orderings for the query.
	 *
	 * @var array
	 */
	public $orders;

	/**
	 * The maximum number of records to return.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The number of records to skip.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * The query union statements.
	 *
	 * @var array
	 */
	public $unions;
  
  /**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'&', '|', '^', '<<', '>>',
		'rlike', 'regexp', 'not regexp',
		'~', '~*', '!~', '!~*',
	);
  
  /**
	 * Set the columns to be selected.
	 *
	 * @param  array  $columns
	 * @return $this
	 */
	public function select($columns = array('*'))	{
		$this->columns = is_array($columns) ? $columns : func_get_args();

		return $this;
	}
  
  /**
	 * Force the query to only return distinct results.
	 *
	 * @return $this
	 */
	public function distinct() {
		$this->distinct = true;

		return $this;
	}
  
  /**
	 * Set the table which the query is targeting.
	 *
	 * @param  string  $table
	 * @return $this
	 */
	public function from($table) {
		$this->from = $table;

		return $this;
	}
}