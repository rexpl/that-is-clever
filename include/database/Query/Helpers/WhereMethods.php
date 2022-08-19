<?php

namespace Mexenus\Database\Query\Helpers;

use Mexenus\Database\Query\Clause\Where;
use Mexenus\Database\Query\Clause\WhereIn;

trait WhereMethods
{
	/**
	 * .. WHERE
	 * 
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function where($column, $operatorORcondition, $condition = null)
	{
		return new Where($this->query, $column, $operatorORcondition, $condition);
	}


	/**
	 * .. WHERE NOT
	 * 
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function whereNot($column, $operatorORcondition, $condition = null)
	{
		return new Where($this->query, $column, $operatorORcondition, $condition, 'NOT');
	}


	/**
	 * .. WHERE column IS NULL
	 * 
	 * @param string $column
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function whereNull($column)
	{
		return new Where($this->query, $column, null, null, 'IS NULL');
	}


	/**
	 * .. WHERE column IS NOT NULL
	 * 
	 * @param string $column
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function whereNotNull($column)
	{
		return new Where($this->query, $column, null, null, 'IS NOT NULL');
	}


	/**
	 * .. WHERE column IN (1, 2, 3, ..)
	 * 
	 * @param string $column
	 * @param array $params
	 * 
	 * @return Mexenus\Database\Query\WhereIn
	 */
	public function whereIn($column, array $params)
	{
		return new WhereIn($this->query, $column, $params);
	}


	/**
	 * .. WHERE column IN (SELECT column FROM exemple)
	 * 
	 * @param string $column
	 * @param string $sql
	 * 
	 * @return Mexenus\Database\Query\WhereIn
	 */
	public function whereInQuery($column, $sql)
	{
		return new WhereIn($this->query, $column, $sql, true);
	}
}