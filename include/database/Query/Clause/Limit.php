<?php

namespace Mexenus\Database\Query\Clause;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

class Limit extends QueryBuilder
{
	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $query
	 * @param int $from
	 * @param int $to
	 * 
	 * @return void
	 */
	public function __construct(Query $query, $from, $to)
	{
		$this->query = $query;

		if (is_null($to)) {

			$this->query->query .= ' LIMIT ' . $from;
		}
		else {

			$this->query->query .= ' LIMIT ' . $from . ', ' . $to;
		}
	}
}