<?php

namespace Clever\Library\Controller\Login;

use Clever\Library\Encryption;
use Clever\Library\Config;
use Clever\Library\Database;

use Clever\Library\Model\User;

class CookieLogin
{
	/**
	 * See if user is logged in via password or cookies.
	 * 
	 * @return bool
	 */
	public function UserLoginWithCookie()
	{
		if ($_SESSION['cookie_login']) return true;
	
		return false;
	}


	/**
	 * Verify the password if logged in with cookies.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return bool
	 */
	public function verifyPassword(Database $database, Config $config)
	{
		if (empty($_POST['password'])) return false;

		$userDB = new User($database);
		$user = $userDB->getLoginDataByID($_SESSION['id_user']);

		if (!password_verify($_POST['password'], $user->password)) return false;

		$crypto = new Encryption();
		$_SESSION['personnal_key'] = $crypto->unlockKey($user->protected_key, $_POST['password']);

		$_SESSION['cookie_login'] = false;
		
		session_regenerate_id(true);

		return true;
	}
}