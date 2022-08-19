<?php

namespace Mexenus\Database\Query\Clause;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\WhereMethods;
use Mexenus\Database\Query\Helpers\OrWhereMethods;
use Mexenus\Database\Query\Helpers\OrderByMethods;
use Mexenus\Database\Query\Helpers\LimitMethods;

class WhereIn extends QueryBuilder
{
	use WhereMethods, OrWhereMethods, OrderByMethods, LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * @param mixed $sqlORparam
	 * @param bool $isQuery
	 * @param string $andORor
	 * 
	 * @return void
	 */
	public function __construct(Query $query, $column, $sqlORparam, $isQuery = false, $andORor = 'AND')
	{
		$this->query = $query;

		$this->query->query .= $this->query->whereStart ? ' ' . $andORor . ' ' : ' WHERE ';
		$this->query->whereStart = true;

		if ($isQuery) return $this->prepareInWithQuery($column, $sqlORparam);

		return $this->prepareInWithParam($column, $sqlORparam);
	}


	/**
	 * 
	 * 
	 * @param string $column
	 * @param string $sql
	 * 
	 * @return void
	 */
	private function prepareInWithQuery($column, $sql)
	{
		$this->query->query .= $column . ' IN (' . $sql .')';
	}


	/**
	 * 
	 * 
	 * @param string $column
	 * @param array $param
	 * 
	 * @return void
	 */
	private function prepareInWithParam($column, $param)
	{
		$count = $this->query->inCount++;

		$this->query->query .= $column . ' IN (';

		$i = 0;

		foreach ($param as $value) {
			
			$this->query->query .= ':in_'.$count.'_value_'.$i.', ';
			$this->query->param['in_'.$count.'_value_'.$i] = $value;

			$i++;
		}

		$this->query->query = substr($this->query->query, 0, -2) . ')';
	}
}