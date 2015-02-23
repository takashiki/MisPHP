<?php
namespace mis\db\query\grammar;

use mis\db\query\grammar\Grammar;
use mis\db\query\Builder;

class MySqlGrammar extends Grammar
{
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
		'lock',
	);
  
  /**
	 * Compile a select query into SQL.
	 *
	 * @param  \mis\db\query\Builder
	 * @return string
	 */
	public function compileSelect(Builder $query) {
		$sql = parent::compileSelect($query);

		if ($query->unions) {
			$sql = '('.$sql.') '.$this->compileUnions($query);
		}

		return $sql;
	}
  
  /**
	 * Compile an update statement into SQL.
	 *
	 * @param  \Illuminate\Database\Query\Builder  $query
	 * @param  array  $values
	 * @return string
	 */
	public function compileUpdate(Builder $query, $values) {
		$sql = parent::compileUpdate($query, $values);

		if (isset($query->orders)) {
			$sql .= ' '.$this->compileOrders($query, $query->orders);
		}

		if (isset($query->limit)) {
			$sql .= ' '.$this->compileLimit($query, $query->limit);
		}

		return rtrim($sql);
	}
  
  /**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value) {
		if ($value === '*') return $value;

		return '`'.str_replace('`', '``', $value).'`';
	}
}