<?php
namespace mis\db\query;

use mis\db\connection\ConnectionInterface;
use mis\db\query\grammar\Grammar;
use mis\db\query\Expression;

class Builder
{
  /**
	 * The database connection instance.
	 *
	 * @var \mis\db\Connection
	 */
	protected $connection;
  
  /**
	 * The database query grammar instance.
	 *
	 * @var \mis\db\grammar\Grammar
	 */
	protected $grammar;
  
  /**
	 * The current query value bindings.
	 *
	 * @var array
	 */
	protected $bindings = array(
		'select' => array(),
		'join'   => array(),
		'where'  => array(),
		'having' => array(),
		'order'  => array(),
	);
  
  /**
	 * An aggregate function and column to be run.
	 *
	 * @var array
	 */
	public $aggregate;
  
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
	 * Indicates whether row locking is being used.
	 *
	 * @var string|bool
	 */
	public $lock;
  
  /**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'%', '!%', '~', 'ilike',
		'&', '|', '^', '<<', '>>',
		'rlike', 'regexp', 'not regexp',
		'~', '~*', '!~', '!~*',
	);
  
  /**
	 * Create a new query builder instance.
	 *
	 * @param  \mis\db\connection\ConnectionInterface  $connection
	 * @param  \mis\db\grammar\Grammar  $grammar
	 * @return void
	 */
	public function __construct(ConnectionInterface $connection, Grammar $grammar) {
		$this->grammar = $grammar;
		$this->connection = $connection;
	}
  
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
	 * Execute the query as a "select" statement.
	 *
	 * @param  array  $columns
	 * @return array|static[]
	 */
	public function get($columns = array('*')) {
		if (is_null($this->columns)) $this->columns = $columns;

		return $this->connection->select($this->toSql(), $this->getBindings());
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
  
  /**
	 * Add a basic where clause to the query.
	 *
	 * @param  mixed   $column
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
  public function where($column, $value = null, $boolean = 'and') {    
    $where = $this->parseWhere($column, $value, $boolean);

    if (is_array($where)) {
      $this->wheres['Basic'][] = $where;
    }
    
    return $this;
  }
  
  /**
	 * parse where query
	 *
	 * @param  string  $column
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return mixed 
	 */
  public function parseWhere($column, $value = null, $boolean = 'and') {
    if (is_array($column)) {
      if (func_num_args() == 2) {
        $boolean = $filter;
      }
      
      return $this->whereNested($column, $boolean);
    }

    list($column, $operator) = $this->parseColumn($column);
    
    if (is_null($value)) {
      return $this->whereNull($column, $boolean, $operator != '=');
    }
    
    if (! $value instanceof Expression) {
			$this->addBinding($value, 'where');
		}
    
    return compact('column', 'operator', 'value', 'boolean');
  }
  
  /**
	 * Add a "where null" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $boolean
	 * @param  bool    $not
	 * @return $this
	 */
	public function whereNull($column, $boolean = 'and', $not = false) {
		$type = $not ? 'NotNull' : 'Null';

		$this->wheres[$type][] = compact('column', 'boolean');

		return $this;
	}
  
  /**
	 * Add a "where nested" clause to the query.
	 *
	 * @param  string  $column
   * @param  string  $boolean
	 * @return $this
	 */
	public function whereNested($columns, $boolean = 'and') {    
    $nested_where = array('boolean' => $boolean);
    
    foreach ($columns as $key => $value) {
      $nested_where['filters'][] = $this->parseWhere($key, $value, $boolean);
    }
    
    $this->wheres['Nested'][] = $nested_where;

		return $this;
	}
  
  /**
	 * parse column of where clause
	 *
	 * @param  string  $column
	 * @return array
	 */
	protected function parseColumn($column)	{
		$sep_pos = strpos($column, ' ');
    if ($sep_pos === false) {
      $operator = '=';
    } else {
      $operator = substr($column, $sep_pos + 1);
      $column = substr($column, 0, $sep_pos);
    }
    
    if (! in_array($operator, $this->operators)) {
      throw new Exception("查询条件无效");
    }
    
    return array($column, $operator);
	}
  
  /**
	 * Add a binding to the query.
	 *
	 * @param  mixed   $value
	 * @param  string  $type
	 * @return $this
	 *
	 * @throws \InvalidArgumentException
	 */
	public function addBinding($value, $type = 'where') {
		if ( ! array_key_exists($type, $this->bindings)) {
			throw new Exception("Invalid binding type: {$type}.");
      //throw new InvalidArgumentException("Invalid binding type: {$type}.");
		}

		if (is_array($value)) {
			$this->bindings[$type] = array_values(array_merge($this->bindings[$type], $value));
		} else {
			$this->bindings[$type][] = $value;
		}

		return $this;
	}
  
  /**
	 * Get the current query value bindings in a flattened array.
	 *
	 * @return array
	 */
	public function getBindings() {
		return array_flatten($this->bindings);
	}
  
  /**
	 * Get the select SQL representation of the query.
	 *
	 * @return string
	 */
	public function toSql() {
		return $this->grammar->compileSelect($this);
	}
}