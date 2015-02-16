<?php
namespace mis\db\teck;

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
}