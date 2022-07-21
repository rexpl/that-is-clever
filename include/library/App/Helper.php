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


	/**
	 * Validate an email address.
	 *
	 * @param string $email
	 * 
	 * @return bool
	 */
	public static function validEmail($email)
	{
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		}

		return true;
	}


	/**
	 * Password strength checker.
	 *
	 * @param string $password
	 * 
	 * @return bool
	 */
	public static function validPassword($password)
	{
		if (!preg_match_all('$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$', $password)) {
			return false;
		}

		return true;
	}
}