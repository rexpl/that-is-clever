<?php

namespace Mexenus\Database\Query\Helpers;

use Mexenus\Database\Query\Clause\OrderBy;

trait OrderByMethods
{
	/**
	 * ORDER BY column ...
	 * 
	 * @param string $column
	 * @param string $order
	 * 
	 * @return Mexenus\Database\Query\OrderBy
	 */
	public function orderBy($column, $order = 'ASC')
	{
		return new OrderBy($this->query, $column, $order);
	}
}