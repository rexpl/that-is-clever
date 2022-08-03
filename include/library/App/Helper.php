<?php 

namespace Clever\Library\App;

class helper
{

	/**
	 * Generate a random string, using a cryptographically secure 
	 * pseudorandom number generator (random_int)
	 *
	 * @param int $length
	 * 
	 * @return string
	 */
	public static function randomString($length): string
	{
		$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		if ($length < 1) {
			throw new \RangeException("Length must be a positive integer.");
		}

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