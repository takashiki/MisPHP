<?php
namespace mis\db\connection;

use PDO;
use Closure;
use DateTime;
use Exception;
use mis\db\grammar\Grammar as QueryGrammar;
use mis\db\query\Builder as QueryBuilder;

class Connection implements ConnectionInterface 
{
  /**
	 * The active PDO connection.
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The active PDO connection used for reads.
	 *
	 * @var PDO
	 */
	protected $readPdo;
  
  /**
	 * The query grammar implementation.
	 *
	 * @var \mis\db\grammar\Grammar
	 */
	protected $queryGrammar;
  
  /**
	 * The default fetch mode of the connection.
	 *
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_ASSOC;
  
  /**
	 * All of the queries run against the connection.
	 *
	 * @var array
	 */
	protected $queryLog = array();
  
  /**
	 * The name of the connected database.
	 *
	 * @var string
	 */
	protected $database;

	/**
	 * The table prefix for the connection.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * The database connection configuration options.
	 *
	 * @var array
	 */
	protected $config = array();
  
  /**
	 * Create a new database connection instance.
	 *
	 * @param  \PDO     $pdo
	 * @param  string   $database
	 * @param  string   $tablePrefix
	 * @param  array    $config
	 * @return void
	 */
	public function __construct(PDO $pdo, $database = '', $tablePrefix = '', array $config = array()) {
		$this->pdo = $pdo;

		$this->database = $database;

		$this->tablePrefix = $tablePrefix;

		$this->config = $config;

		$this->useDefaultQueryGrammar();
	}
  
  /**
	 * Set the query grammar to the default implementation.
	 *
	 * @return void
	 */
	public function useDefaultQueryGrammar() {
		$this->queryGrammar = $this->getDefaultQueryGrammar();
	}

	/**
	 * Get the default query grammar instance.
	 *
	 * @return \mis\db\grammar\Grammar
	 */
	protected function getDefaultQueryGrammar() {
		return new QueryGrammar;
	}
  
  /**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string  $table
	 * @return \mis\db\query\Builder
	 */
	public function table($table) {
		$query = new QueryBuilder($this, $this->getQueryGrammar());

		return $query->from($table);
	}
  
  /**
	 * Get the query grammar used by the connection.
	 *
	 * @return \Illuminate\Database\Query\Grammars\Grammar
	 */
	public function getQueryGrammar() {
		return $this->queryGrammar;
	}
  
  /**
	 * Run a select statement against the database.
	 *
	 * @param  string  $query
	 * @param  array  $bindings
	 * @param  bool  $useReadPdo
	 * @return array
	 */
	public function select($query, $bindings = array(), $useReadPdo = true) {
		return $this->run($query, $bindings, $useReadPdo);
	}
  
  /**
	 * Get the PDO connection to use for a select query.
	 *
	 * @param  bool  $useReadPdo
	 * @return \PDO
	 */
	protected function getPdoForSelect($useReadPdo = true) {
		return $useReadPdo ? $this->getReadPdo() : $this->getPdo();
	}
  
  /**
	 * Run a SQL statement and log its execution context.
	 *
	 * @param  string    $query
	 * @param  array     $bindings
	 * @param  \Closure  $callback
	 * @return mixed
	 *
	 * @throws \mis\db\QueryException
	 */
	protected function run($query, $bindings, $useReadPdo) {
		$start = microtime(true);
    
    $statement = $this->getPdoForSelect($useReadPdo)->prepare($query);

	  $statement->execute($this->prepareBindings($bindings));

		$result = $statement->fetchAll($this->getFetchMode());

		$time = $this->getElapsedTime($start);

		return $result;
	}
  
  /**
	 * Prepare the query bindings for execution.
	 *
	 * @param  array  $bindings
	 * @return array
	 */
	public function prepareBindings(array $bindings) {
		$grammar = $this->getQueryGrammar();

		foreach ($bindings as $key => $value) {
			if ($value instanceof DateTime) {
				$bindings[$key] = $value->format($grammar->getDateFormat());
			}	elseif ($value === false) {
				$bindings[$key] = 0;
			}
		}

		return $bindings;
	}
  
  /**
	 * Get the current PDO connection.
	 *
	 * @return \PDO
	 */
	public function getPdo() {
		return $this->pdo;
	}
  
  /**
	 * Get the current PDO connection used for reading.
	 *
	 * @return \PDO
	 */
	public function getReadPdo() {
		//if ($this->transactions >= 1) return $this->getPdo();

		return $this->readPdo ?: $this->pdo;
	}
  
  /**
	 * Get the name of the connected database.
	 *
	 * @return string
	 */
	public function getDatabaseName() {
		return $this->database;
	}

	/**
	 * Set the name of the connected database.
	 *
	 * @param  string  $database
	 * @return string
	 */
	public function setDatabaseName($database) {
		$this->database = $database;
	}

	/**
	 * Get the table prefix for the connection.
	 *
	 * @return string
	 */
	public function getTablePrefix() {
		return $this->tablePrefix;
	}

	/**
	 * Set the table prefix in use by the connection.
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setTablePrefix($prefix)	{
		$this->tablePrefix = $prefix;

		$this->getQueryGrammar()->setTablePrefix($prefix);
	}

	/**
	 * Set the table prefix and return the grammar.
	 *
	 * @param  \mis\db\grammar\Grammar  $grammar
	 * @return \mis\db\grammar\Grammar
	 */
	public function withTablePrefix($grammar) {
		$grammar->setTablePrefix($this->tablePrefix);

		return $grammar;
	}
  
  /**
	 * Get the default fetch mode for the connection.
	 *
	 * @return int
	 */
	public function getFetchMode() {
		return $this->fetchMode;
	}
  
  /**
	 * Set the default fetch mode for the connection.
	 *
	 * @param  int  $fetchMode
	 * @return int
	 */
	public function setFetchMode($fetchMode) {
		$this->fetchMode = $fetchMode;
	}
  
  /**
	 * Get the elapsed time since a given starting point.
	 *
	 * @param  int    $start
	 * @return float
	 */
	protected function getElapsedTime($start) {
		return round((microtime(true) - $start) * 1000, 2);
	}
}