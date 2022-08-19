<?php

namespace Mexenus\Database\Query\Statement;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\WhereMethods;
use Mexenus\Database\Query\Helpers\OrderByMethods;
use Mexenus\Database\Query\Helpers\LimitMethods;

class Update extends QueryBuilder
{
	use WhereMethods, OrderByMethods, LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * @param array $updateColumns
	 * 
	 * @return void
	 */
	public function __construct(Query $query, array $updateColumns = [])
	{
		$query->query = 'UPDATE ' . $query->table . ' SET ';

		$i = 0;

		foreach ($updateColumns as $key => $value) {

			$query->query .= $key . ' = :value_' . $i . ', ';
			$query->param['value_'.$i] = $value;

			$i++;
		}

		$query->query = substr($query->query, 0, -2);

		$this->query = $query;
	}
}