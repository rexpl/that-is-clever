<?php

namespace Clever\Library\Controller\Login;

use Clever\Library\Database;
use Clever\Library\Config;

use Clever\Library\Model\PersistantLogin;

class Logout
{
	/**
	 * Log the user out.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * @param bool $all
	 * 
	 * @return bool
	 */
	public function logout(Database $database, Config $config)
	{
		$login = new PersistantLogin($database);

		if ($_GET['all']) {

			$login->deleteAllByUserID($_SESSION['id_user']);
		}
		else {

			$login->deleteBySerial($_COOKIE['serial']);
		}

		session_destroy();
		session_regenerate_id(true);

		setcookie("serial", null, 0, "/", "", $config->get('cookie_secure'), true);
		setcookie("token", null, 0, "/", "", $config->get('cookie_secure'), true);

		return true;
	}
}