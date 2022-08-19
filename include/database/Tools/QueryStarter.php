<?php

namespace Mexenus\Database\Tools;

use Mexenus\Database\Query\Statement\Select;
use Mexenus\Database\Query\Statement\Update;
use Mexenus\Database\Query\Statement\Insert;
use Mexenus\Database\Query\Statement\Delete;

trait QueryStarter
{
	abstract protected function newQuery();


	/**
	 * New Select() instance.
	 * 
	 * @param array $columns
	 * 
	 * @return Mexenus\Database\Query\Select
	 */
	public function select(array $columns = [])
	{
		return new Select($this->newQuery(), $columns);
	}


	/**
	 * New Insert() instance.
	 * 
	 * @return Mexenus\Database\Query\Insert
	 */
	public function insert()
	{
		return new Insert($this->newQuery());
	}


	/**
	 * New Update() instance.
	 * 
	 * @param array $columns
	 * 
	 * @return Mexenus\Database\Query\Update
	 */
	public function update(array $columns)
	{
		return new Update($this->newQuery(), $columns);
	}


	/**
	 * New Delete() instance.
	 * 
	 * @return Mexenus\Database\Query\Delete
	 */
	public function delete()
	{
		return new Delete($this->newQuery());
	}
}