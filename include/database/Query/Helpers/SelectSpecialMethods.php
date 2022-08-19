<?php

namespace Mexenus\Database\Query\Helpers;

trait SelectSpecialMethods
{
	/**
	 * @return Mexenus\Database\Query\Select
	 */
	public function count($column)
	{
		$this->query->query = 'SELECT COUNT(' . $column . ') FROM ' . $this->query->table;

		return $this;
	}


	/**
	 * @return Mexenus\Database\Query\Select
	 */
	public function avg()
	{
		$this->query->query = 'SELECT AVG(' . $column . ') FROM ' . $this->query->table;

		return $this;
	}


	/**
	 * @return Mexenus\Database\Query\Select
	 */
	public function sum()
	{
		$this->query->query = 'SELECT SUM(' . $column . ') FROM ' . $this->query->table;

		return $this;
	}


	/**
	 * @return Mexenus\Database\Query\Select
	 */
	public function min()
	{
		$this->query->query = 'SELECT MIN(' . $column . ') FROM ' . $this->query->table;

		return $this;
	}


	/**
	 * @return Mexenus\Database\Query\Select
	 */
	public function max()
	{
		$this->query->query = 'SELECT MAX(' . $column . ') FROM ' . $this->query->table;

		return $this;
	}
}