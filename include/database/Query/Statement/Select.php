<?php

namespace Mexenus\Database\Query\Statement;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\SelectSpecialMethods;
use Mexenus\Database\Query\Helpers\WhereMethods;
use Mexenus\Database\Query\Helpers\OrderByMethods;
use Mexenus\Database\Query\Helpers\LimitMethods;

class Select extends QueryBuilder
{
	use SelectSpecialMethods, WhereMethods, OrderByMethods, LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * @param array $columns
	 * 
	 * @return void
	 */
	public function __construct(Query $query, array $columns = [])
	{
		$this->query = $query;
		$this->query->select = true;

		$this->query->selectColumnsCount = count($columns);
		$columns = empty($columns) ? '*' : implode(', ', $columns);

		$this->query->query = 'SELECT ' . $columns . ' FROM ' . $this->query->table;
	}
}