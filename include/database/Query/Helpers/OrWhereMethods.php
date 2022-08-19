<?php

namespace Mexenus\Database\Query\Helpers;

use Mexenus\Database\Query\Clause\Where;
use Mexenus\Database\Query\Clause\WhereIn;

trait OrWhereMethods
{
	/**
	 * The following methods are not in trait WhereMethods()
	 * because Where() needs to have been initialized first.
	 * 
	 * ex: you cannot do SELECT column WHERE OR ..
	 * ex: expected result SELECT column WHERE condition OR ..
	 */


	/**
	 * WHERE ... OR
	 * 
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function orWhere($column, $operatorORcondition, $condition = null)
	{
		return new Where($this->query, $column, $operatorORcondition, $condition, null, 'OR');
	}


	/**
	 * WHERE ... OR
	 * 
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function orWhereNot($column, $operatorORcondition, $condition = null)
	{
		return new Where($this->query, $column, $operatorORcondition, $condition, 'NOT', 'OR');
	}


	/**
	 * WHERE ... OR column IS NULL
	 * 
	 * @param string $column
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function orWhereNull($column)
	{
		return new Where($this->query, $column, null, null, 'IS NULL', 'OR');
	}


	/**
	 * WHERE ... OR column IS NOT NULL
	 * 
	 * @param string $column
	 * 
	 * @return Mexenus\Database\Query\Where
	 */
	public function orWhereNotNull($column)
	{
		return new Where($this->query, $column, null, null, 'IS NOT NULL', 'OR');
	}


	/**
	 * .. WHERE ... OR column IN (1, 2, 3, ..)
	 * 
	 * @param string $column
	 * @param array $params
	 * 
	 * @return Mexenus\Database\Query\WhereIn
	 */
	public function orWhereIn($column, array $params)
	{
		return new WhereIn($this->query, $column, $params, false, 'OR');
	}


	/**
	 * .. WHERE ... OR column IN (SELECT column FROM exemple)
	 * 
	 * @param string $column
	 * @param string $sql
	 * 
	 * @return Mexenus\Database\Query\WhereIn
	 */
	public function orWhereInQuery($column, $sql)
	{
		return new WhereIn($this->query, $column, $sql, true, 'OR');
	}
}