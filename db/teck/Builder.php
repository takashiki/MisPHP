<?php
namespace mis\db\teck;

use mis\db\query\Builder as QueryBuilder;

class Builder
{
  /**
	 * The base query builder instance.
	 *
	 * @var \mis\db\query\Builder
	 */
	protected $query;

	/**
	 * The model being queried.
	 *
	 * @var \mis\db\teck\Model
	 */
	protected $model;
  
  /**
	 * The methods that should be returned from query builder.
	 *
	 * @var array
	 */
	protected $passthru = array(
		'toSql', 'lists', 'insert', 'insertGetId', 'pluck', 'count',
		'min', 'max', 'avg', 'sum', 'exists', 'getBindings',
	);
  
  /**
	 * Create a new Teck query builder instance.
	 *
	 * @param  \mis\db\query\Builder  $query
	 * @return void
	 */
	public function __construct(QueryBuilder $query) {
		$this->query = $query;
	}
  
  /**
	 * Set a model instance for the model being queried.
	 *
	 * @param  \mis\db\teck\Model  $model
	 * @return $this
	 */
	public function setModel(Model $model) {
		$this->model = $model;

		$this->query->from($model->getTable());

		return $this;
	}
  
  /**
	 * Dynamically handle calls into the query instance.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$result = call_user_func_array(array($this->query, $method), $parameters);

    return $result;
		//return in_array($method, $this->passthru) ? $result : $this;
	}
}