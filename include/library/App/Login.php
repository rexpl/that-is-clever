<?php 

namespace Clever\Library\App;

use Clever\Library\App\Encryption;
use Clever\Library\App\Config;
use Clever\Library\App\Database;
use Clever\Library\App\Helper;

use Clever\Library\App\Model\User;
use Clever\Library\App\Model\PersistantLogin;

class Login
{
	/**
	 * Verify login credentials and log the user in.
	 *
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
	 * 
	 * @return array
	 */
	public static function login(Database $database, Config $config)
	{
		/**
		 * Verify we have all necessary data.
		 */
		if (empty($_POST['username']) || empty($_POST['password'])) {

			return [
				"succes" => false,
				"message" => t('login_fill_fields'),
			];
		}


		$userDB = new User($database);
		$user = $userDB->getLoginDataByUsername($_POST['username']);


		/**
		 * Silently block the user if there are more than 15 failed login attemps.
		 * Forcing the user to reset his password.
		 */
		if (!$user || $user->failed_login_count >= 15) {

			return [
				"succes" => false,
				"message" => t('login_failed_attempt'),
			];
		}


		if (!password_verify($_POST['password'], $user->password)) {

			$userDB ->incrementFailedLoginCountByID($user->id);
			
			return [
				"succes" => false,
				"message" => t('login_failed_attempt'),
			];
		}


		session_regenerate_id(true);


		$crypto = new Encryption();
		$_SESSION['personnal_key'] = $crypto->unlockKey($user->protected_key, $_POST['password']);

		$_SESSION['id_user'] = $user->id;
		$_SESSION['username'] = $_POST['username'];
		$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['cookie_login'] = false;


		/**
		 * We set the necessary cookies for persistant login.
		 */
		$login = new PersistantLogin($database);

		while (true) {

			//serial has to be unique
			$serial = Helper::randomString(128);
			if (!$login->serialExist($serial)) break;
		}

		setcookie("serial", $serial, time()+5443200, "/", "", $config->get('cookie_secure'), true);

		$token = Helper::randomString(64);
		setcookie("token", $token, time()+1814400, "/", "", $config->get('cookie_secure'), true);


		$login->new($user->id, $serial, password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]));


		return [
			"succes" => true,
		];
	}


	/**
	 * Log the user out.
	 *
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
	 * @param bool $all
	 * 
	 * @return bool
	 */
	public static function logout(Database $database, Config $config, $all)
	{
		$login = new PersistantLogin($database);

		if ($all) {

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


	/**
	 * Verify if the user is login.
	 *
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
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


	/**
	 * Verify the password if logged in with cookies.
	 *
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
	 * @param bool $all
	 * 
	 * @return bool
	 */
	public static function verifyPassword(Database $database, Config $config)
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