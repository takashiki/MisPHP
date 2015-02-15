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
	 * The database query grammar instance.
	 *
	 * @var \mis\db\grammar\Grammar
	 */
	protected $grammar;
  
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
		'%', '!%', '~', 'ilike',
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
    if (is_array($column)) {
      if (func_num_args() == 2) {
        $boolean = $filter;
      }
      
      return $this->whereNested(function($query) use ($column) {
        foreach ($column as $key => $value) {
          $query->where($key, $value, $boolean);
        }
      }, $boolean);
    }
    
    list($column, $operator) = $this->parseColumn($column);
    
    if (is_null($value)) {
      return $this->whereNull($column, $boolean, $operator != '=');
    }
    
    $type = 'Basic';
    $this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');
    
    return $this;
  }

  /**
	 * Add an "or where" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @return $this|static
	 */
	public function orWhere($column, $filter = null)
	{
		return $this->where($column, $filter, 'or');
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

		$this->wheres[] = compact('type', 'column', 'boolean');

		return $this;
	}
  
  /**
	 * parse column of where clause
	 *
	 * @param  string  $column
	 * @return array
	 */
	protected function parseColumn($column)
	{
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
    
    return compact($column, $operator);
	}
  
  /**
	 * Get the SQL representation of the query.
	 *
	 * @return string
	 */
	public function toSql() {
		return $this->grammar->compileSql($this);
	}
}