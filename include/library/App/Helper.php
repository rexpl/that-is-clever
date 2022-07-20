<?php 

namespace Clever\Library\App;

class helper
{
	/**
	 * Generate random suit of charachters.
	 *
	 * @param int $length
	 * 
	 * @return string
	 */
	public static function randomString($length)
	{
		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$pieces = [];
		$max = mb_strlen($keyspace, '8bit') - 1;
		for ($i = 0; $i < $length; ++$i) {
			$pieces []= $keyspace[random_int(0, $max)];
		}
		return implode('', $pieces);
	}
}