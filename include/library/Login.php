<?php 

namespace Clever\Library;

use Clever\Library\Config;
use Clever\Library\Database;
use Clever\Library\Helper;

use Clever\Library\Model\User;
use Clever\Library\Model\PersistantLogin;

class Login
{
	/**
	 * Verify if the user is login.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return bool
	 */
	public static function verifyLogin(Database $database, Config $config)
	{
		/**
		 * User ID present and IP match, we consider the user logged in.
		 */
		if (isset($_SESSION['id_user'], $_SESSION['remote_ip']) && $_SESSION['remote_ip'] == $_SERVER['REMOTE_ADDR']) {

			return true;
		}


		/**
		 * Verify if we have neccessary cookies to log the user in.
		 */
		if (!isset($_COOKIE['serial'], $_COOKIE['token'])) return false;


		$login = new PersistantLogin($database);
		$serial = $login->findSerial($_COOKIE['serial']);

		if (!$serial) return false;


		/**
		 * If the serial matches but the token doesn't, the token is probably already used.
		 * Chances are the user is victim of an xss atack so we log the user out on every device,
		 * forcing the user to reenter his password to login.
		 */
		if (!password_verify($_COOKIE['token'], $serial->token)) {

			$login->deleteAllByUserID($serial->id_user);
			return false;
		}


		/**
		 * Token match user is login.
		 */
		$user = new User($database);
		$_SESSION['username'] = $user->getUsernameByID($serial->id_user);
		
		$_SESSION['id_user'] = $serial->id_user;
		$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['cookie_login'] = true;


		/**
		 * We update the token so it can't be reused.
		 */
		$token = Helper::randomString(64);
		$login->updateToken($serial->id, password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]));
		setcookie("token", $token, time()+1814400, "/", "", $config->get('cookie_secure'), true);

		return true;
	}
}