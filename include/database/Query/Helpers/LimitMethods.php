<?php

namespace Mexenus\Database\Query\Helpers;

use Mexenus\Database\Query\Clause\Limit;

trait LimitMethods
{
	/**
	 * LIMIT ...
	 * 
	 * @param int $from
	 * @param int $to
	 * 
	 * @return Mexenus\Database\Query\Limit
	 */
	public function limit($from, $to = null)
	{
		return new Limit($this->query, $from, $to);
	}
}