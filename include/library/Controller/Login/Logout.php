<?php

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

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

		if (isset($_GET['all'])) {

			$login->delete()->where('id_user', $_SESSION['id_user'])->execute();
		}
		else {

			$login->delete()->where('serial', $_COOKIE['serial'])->execute();
		}

		session_destroy();

		setcookie("serial", null, 0, "/", "", $config->get('cookie_secure'), true);
		setcookie("token", null, 0, "/", "", $config->get('cookie_secure'), true);

		return true;
	}
}