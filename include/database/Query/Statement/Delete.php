<?php

namespace Mexenus\Database\Query\Statement;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

use Mexenus\Database\Query\Helpers\WhereMethods;
use Mexenus\Database\Query\Helpers\OrderByMethods;
use Mexenus\Database\Query\Helpers\LimitMethods;

class Delete extends QueryBuilder
{
	use WhereMethods, OrderByMethods, LimitMethods;


	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * 
	 * @return void
	 */
	public function __construct(Query $query)
	{
		$query->query = 'DELETE FROM ' . $query->table;

		$this->query = $query;
	}
}