<?php

namespace Mexenus\Database\Query\Statement;

use Mexenus\Database\Query\Query;
use Mexenus\Database\Query\QueryBuilder;

class Insert extends QueryBuilder
{
	/**
	 * @var Mexenus\Database\Query\Query
	 */
	protected $query;


	/**
	 * @param Query $quere
	 * @param array $columns
	 * 
	 * @return void
	 */
	public function __construct(Query $query)
	{
		$query->query = 'INSERT INTO ' . $query->table;

		$this->query = $query;
	}


	/**
	 * @param array $params
	 * 
	 * @return mixed
	 */
	public function values(array $params, $default = true)
	{
		if (array_keys($params) !== range(0, count($params) - 1)) {

			if ($default) $params = array_merge($this->query->default, $params);

			$this->query->query .= ' (' . implode(', ', array_keys($params)) . ') VALUES (:' . implode(', :', array_keys($params)) . ')';
			$this->query->param = $params;
			
			return $this->execute();
		}

		$this->query->query .= ' (' . implode(', ', $params) . ') VALUES (:' . implode(', :', $params) . ')';

		return $this->prepare();
	}
}