<?php 

namespace Clever\Library\App;

use Clever\Library\App\Encryption;
use Clever\Library\App\Config;
use Clever\Library\App\Database;

use Clever\Library\App\Model\User;

class Register
{
	/**
	 * Verify if username already in use.
	 * 
	 * @param Clever\Library\App\Database
	 * @param string
	 * 
	 * @return bool
	 */
	public static function username(Database $database, $username)
	{
		$user = new User($database);

		if (!$user->usernameExist($username)) return false;

		return true;
	}


	/**
	 * Try to register the user.
	 * 
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
	 * 
	 * @return array
	 */
	public static function register(Database $database, Config $config)
	{
		
	}
}