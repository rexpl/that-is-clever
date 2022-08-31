<?php

namespace Mexenus\Database;

use Mexenus\Database\Tools\QueryStarter;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryModel;

abstract class Model
{
	use QueryStarter;


	/** 
	 * @var Database
	 */
	private $database;


	/**
	 * @return void
	 */
	final public function __construct(Database $database)
	{
		$this->database = $database;

		if (method_exists($this, 'modelConstructor')) {

			$arguments = func_get_args();
			unset($arguments[0]);

			call_user_func_array([$this, 'modelConstructor'], $arguments);
		}

		if (!isset($this->table)) {

			trigger_error("No table defined in " . get_class($this) . ".", E_USER_ERROR);
			die();
		}

		$this->primary = $this->primary ?? 'id';

		$this->hidden = $this->hidden ?? [];
		$this->default = $this->default ?? [];
	}


	/**
	 * New Query() instance.
	 * 
	 * @return Mexenus\Database\Query
	 */
	private function newQuery()
	{
		$query = new Query($this->database);

		$query->table = $this->table;

		$query->primary = $this->primary;

		$query->hidden = $this->hidden;
		$query->default = $this->default;

		return $query;
	}


	/**
	 * Get a record by it's primary key.
	 * 
	 * @param string $name
	 * 
	 * @return mixed
	 */
	private function findByPrimary($param)
	{
		return $this->select()->where($this->primary, $param)->first();
	}


	/**
	 * Get a record by specific column name.
	 * 
	 * @param mixed $param
	 * @param string $column
	 * 
	 * @return mixed
	 */
	private function findByColumn($param, $operatorORcolumn, $column)
	{
		if (is_null($column)) return $this->select()->where($operatorORcolumn, $param)->first();

		return $this->select()->where($column, $operatorORcolumn, $param)->first();
	}


	/**
	 * Shortcut to get a record with just one param.
	 * 
	 * @param mixed $param
	 * @param string $column
	 * 
	 * @return mixed
	 */
	public function find($param, $operatorORcolumn = null, $column = null)
	{
		if (is_null($operatorORcolumn)) return $this->findByPrimary($param);

		return $this->findByColumn($param, $operatorORcolumn, $column);
	}


	/**
	 * Create new record of specidied table.
	 * 
	 * @return mixed
	 */
	public function new()
	{
		return new QueryModel($this->database, $this->table, $this->primary, $this->hidden, $this->default, true);
	}
}