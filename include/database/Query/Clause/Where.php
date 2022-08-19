<?php

namespace Mexenus\Database\Query\Clause;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\WhereMethods;
use Mexenus\Database\Query\Helpers\OrWhereMethods;
use Mexenus\Database\Query\Helpers\OrderByMethods;
use Mexenus\Database\Query\Helpers\LimitMethods;

class Where extends QueryBuilder
{
	use WhereMethods, OrWhereMethods, OrderByMethods, LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * Supported operators.
	 * 
	 * @var array
	 */
	private $operators = [
		'=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
		'like', 'like binary', 'not like', '<<', '>>', 'similar to',
		'not similar to',
	];


	/**
	 * @param Query $query
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * @param string $extra
	 * @param string $andORor
	 * 
	 * @return void
	 */
	public function __construct(Query $query, $column, $operatorORcondition, $condition, $extra = null, $andORor = 'AND')
	{
		$this->query = $query;

		$this->query->query .= $this->query->whereStart ? ' ' . $andORor . ' ' : ' WHERE ';
		$this->query->whereStart = true;

		if (is_null($extra)) return $this->prepareWhere($column, $operatorORcondition, $condition);

		if ($extra == 'NOT') return $this->prepareWhere($column, $operatorORcondition, $condition, true);

		return $this->prepareExtra($column, $extra);
	}


	/**
	 * 
	 * 
	 * @param string $operator
	 * 
	 * @return void
	 */
	private function unsupportedOperator($operator)
	{
		trigger_error("Unsupported operator \"" . $operator . "\" in WHERE query.", E_USER_ERROR);
		die();
	}


	/**
	 * 
	 * 
	 * @param string $column
	 * @param string $operatorORcondition
	 * @param string $condition
	 * @param bool $not
	 * 
	 * @return void
	 */
	private function prepareWhere($column, $operatorORcondition, $condition, $not = false)
	{
		if (is_null($condition)) {

			$operator = '=';
			$param = $operatorORcondition;
		}
		else {

			$operator = $operatorORcondition;
			$param = $condition;
		}

		if (!in_array(strtolower($operator), $this->operators)) return $this->unsupportedOperator($operator);

		$i = count($this->query->param);

		if ($not) $this->query->query .= 'NOT ';

		$this->query->query .= $column . ' ' . $operator . ' :value_' . $i;
		$this->query->param['value_'.$i] = $param;
	}


	/**
	 * 
	 * 
	 * @param string $column
	 * @param string $extra
	 * 
	 * @return void
	 */
	private function prepareExtra($column, $extra)
	{
		$this->query->query .= $column . ' ' . $extra;
	}
}