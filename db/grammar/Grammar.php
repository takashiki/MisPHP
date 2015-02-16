<?php
namespace mis\db\grammar;

use mis\db\query\Builder;

class Grammar
{
  /**
	 * The grammar table prefix.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';
  
  /**
	 * The components that make up a select clause.
	 *
	 * @var array
	 */
	protected $selectComponents = array(
		'aggregate',
		'columns',
		'from',
		'joins',
		'wheres',
		'groups',
		'havings',
		'orders',
		'limit',
		'offset',
		'unions',
		'lock',
	);
  
  /**
	 * Compile a select query into SQL.
	 *
	 * @param  \mis\db\query\Builder
	 * @return string
	 */
	public function compileSelect(Builder $query) {
		if (is_null($query->columns)) $query->columns = array('*');

		return trim($this->concatenate($this->compileComponents($query)));
	}
  
  /**
	 * Compile the components necessary for a select clause.
	 *
	 * @param  \mis\db\query\Builder
	 * @return array
	 */
	protected function compileComponents(Builder $query) {
		$sql = array();

		foreach ($this->selectComponents as $component) {
			if ( ! is_null($query->$component)) {
				$method = 'compile'.ucfirst($component);

				$sql[$component] = $this->$method($query, $query->$component);
			}
		}

		return $sql;
	}
  
  /**
	 * Concatenate an array of segments, removing empties.
	 *
	 * @param  array   $segments
	 * @return string
	 */
	protected function concatenate($segments) {
		return implode(' ', array_filter($segments, function($value) {
			return (string) $value !== '';
		}));
	}
  
  /**
	 * Compile an aggregated select clause.
	 *
	 * @param  \mis\db\query\Builder $query
	 * @param  array  $aggregate
	 * @return string
	 */
	protected function compileAggregate(Builder $query, $aggregate) {
		$column = $this->columnize($aggregate['columns']);

		if ($query->distinct && $column !== '*') {
			$column = 'distinct '.$column;
		}

		return 'select ' . $aggregate['function'] . '(' . $column . ') as ' . $aggregate['function'] . '_' . $aggregate['columns'];
	}
  
  /**
	 * Compile the "select *" portion of the query.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @param  array  $columns
	 * @return string
	 */
	protected function compileColumns(Builder $query, $columns)	{
		if ( ! is_null($query->aggregate)) return;

		$select = $query->distinct ? 'select distinct ' : 'select ';

		return $select.$this->columnize($columns);
	}
  
  /**
	 * Compile the "from" portion of the query.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @param  string  $table
	 * @return string
	 */
	protected function compileFrom(Builder $query, $table) {
		return 'from '.$this->wrapTable($table);
	}
  
  /**
	 * Compile the "where" portions of the query.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @return string
	 */
	protected function compileWheres(Builder $query) {
		$sql = array();

		if (is_null($query->wheres)) return '';

		foreach ($query->wheres as $type => $where) {
			$method = "where{$type}";

			$sql[] = $where['boolean'].' '.$this->$method($query, $where);
		}

		if (count($sql) > 0) {
			$sql = implode(' ', $sql);

			return 'where '.preg_replace('/and |or /', '', $sql, 1);
		}

		return '';
	}
  
  /**
	 * Compile a basic where clause.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereBasic(Builder $query, $where)
	{
		$value = $this->parameter($where['value']);

		return $this->wrap($where['column']).' '.$where['operator'].' '.$value;
	}
  
  /**
	 * Compile a "where null" clause.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereNull(Builder $query, $where) {
		return $this->wrap($where['column']).' is null';
	}

	/**
	 * Compile a "where not null" clause.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @param  array  $where
	 * @return string
	 */
	protected function whereNotNull(Builder $query, $where) {
		return $this->wrap($where['column']).' is not null';
	}
  
  /**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @param  bool    $prefixAlias
	 * @return string
	 */
	public function wrap($value) {
		if (strpos(strtolower($value), ' as ') !== false) {
			$segments = explode(' ', $value);

			return $this->wrap($segments[0]) . ' as ' . $this->wrapValue($segments[2]);
		}

		$wrapped = array();

		$segments = explode('.', $value);

		foreach ($segments as $key => $segment) {
			if ($key == 0 && count($segments) > 1) {
				$wrapped[] = $this->wrapTable($segment);
			}	else {
				$wrapped[] = $this->wrapValue($segment);
			}
		}

		return implode('.', $wrapped);
	}
  
	/**
	 * Wrap an array of values.
	 *
	 * @param  array  $values
	 * @return array
	 */
	public function wrapArray(array $values) {
		return array_map(array($this, 'wrap'), $values);
	}
  
  /**
	 * Wrap a table in keyword identifiers.
	 *
	 * @param  string  $table
	 * @return string
	 */
	public function wrapTable($table) {
		return $this->wrap($this->tablePrefix.$table, true);
	}
  
  /**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value) {
		if ($value === '*') return $value;

		return '"'.str_replace('"', '""', $value).'"';
	}

	/**
	 * Convert an array of column names into a delimited string.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	public function columnize(array $columns) {
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}
  
  /**
	 * Get the format for database stored dates.
	 *
	 * @return string
	 */
	public function getDateFormat() {
		return 'Y-m-d H:i:s';
	}

	/**
	 * Get the grammar's table prefix.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the grammar's table prefix.
	 *
	 * @param  string  $prefix
	 * @return $this
	 */
	public function setTablePrefix($prefix) {
		$this->tablePrefix = $prefix;

		return $this;
	}
}