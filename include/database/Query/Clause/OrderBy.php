<?php

namespace Mexenus\Database\Query\Clause;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\LimitMethods;

class OrderBy extends QueryBuilder
{
	use LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * @param string $column
	 * @param string $order
	 * 
	 * @return void
	 */
	public function __construct(Query $query, $column, $order)
	{
		$this->query = $query;

		$this->query->query .= ' ORDER BY ' . $column . ' ' . $order;
	}
}