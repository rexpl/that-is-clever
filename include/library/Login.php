<?php 

namespace Clever\Library;

use Mexenus\Database\Database;

use Clever\Library\Config;
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


		$loginDB = new PersistantLogin($database);
		$login = $loginDB->find($_COOKIE['serial'], 'serial');

		if (!$login) return false;


		/**
		 * If the serial matches but the token doesn't, the token is probably already used.
		 * Chances are the user is victim of an xss atack so we log the user out on every device,
		 * forcing the user to reenter his password to login.
		 */
		if (!password_verify($_COOKIE['token'], $login->token)) {

			$loginDB->delete()->where('id_user', $login->id_user)->execute();
			return false;
		}


		/**
		 * Token match user is login.
		 */
		$user = new User($database);
		$_SESSION['username'] = $user->find($login->id_user)->username;
		
		$_SESSION['id_user'] = $login->id_user;
		$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['cookie_login'] = true;


		/**
		 * We update the token so it can't be reused.
		 */
		$token = Helper::randomString(64);

		$login->token = password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$login->save();

		setcookie("token", $token, time()+1814400, "/", "", $config->get('cookie_secure'), true);

		return true;
	}
}