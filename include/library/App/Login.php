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
	 * @param id
	 * 
	 * @return bool
	 */
	public static function login(Database $database)
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


		$crypto = new Encryption();
		$_SESSION['personnal_key'] = $crypto->unlockKey($user->protected_key, $_POST['password']);

		$_SESSION['id_user'] = $user->id;
		$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['cookie_login'] = false;


		/**
		 * We set the necessary cookies for persistant login.
		 */
		$login = new PersistantLogin();


		$found = false;
		while ($found == true) {

			//serial has to be unique
			$serial = Helper::randomString(128);
			$found = $login->serialExist($serial);
		}

		setcookie("serial", $serial, time()+5443200, "/", $config->get('url'), $config->get('cookie_secure'), true);

		$token = Helper::randomString(64);
		setcookie("token", $token, time()+1814400, "/", $config->get('url'), $config->get('cookie_secure'), true);


		$login->new($user->id, $serial, password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]));


		return [
			"succes" => true,
		];
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
		if (isset($_SESSION['id_user']) && $_SESSION['remote_ip'] == $_SERVER['REMOTE_ADDR']) return true;


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
		if (password_verify($_COOKIE['token'], $serial->token)) {

			$login->deleteAllByUserID($serial->id_user);
			return false;
		}


		/**
		 * Token match user is login.
		 */
		$_SESSION['id_user'] = $serial->id_user;
		$_SESSION['remote_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['cookie_login'] = true;


		/**
		 * We update the token so it can't be reused.
		 */
		$token = Helper::randomString(64);
		$login->updateToken($serial->id, password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]));
		setcookie("token", $token, time()+1814400, "/", $config->get('url'), $config->get('cookie_secure'), true);

		return true;
	}
}