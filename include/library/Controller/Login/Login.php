<?php

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

use Clever\Library\Encryption;
use Clever\Library\Config;
use Clever\Library\Helper;

use Clever\Library\Model\User;
use Clever\Library\Model\PersistantLogin;

class Login
{
	/**
	 * Verify login credentials and log the user in.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	public function login(Database $database, Config $config)
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
		$user = $userDB->find($_POST['username'], '= BINARY', 'username');


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

			$user->failed_login_count++;
			$user->save();
			
			return [
				"succes" => false,
				"message" => t('login_failed_attempt'),
			];
		}


		if ($user->failed_login_count > 0) {
			
			$user->failed_login_count = 0;
			$user->save();
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

		$newLogin = $login->new();
		$newLogin->id_user = $_SESSION['id_user'];

		while (true) {

			//serial has to be unique
			$newLogin->serial = Helper::randomString(128);
			if (!$login->serialExist($newLogin->serial)) break;
		}

		setcookie("serial", $newLogin->serial, time()+5443200, "/", "", $config->get('cookie_secure'), true);

		$token = Helper::randomString(64);
		setcookie("token", $token, time()+1814400, "/", "", $config->get('cookie_secure'), true);


		$newLogin->token = password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$newLogin->save();

		return [
			"succes" => true,
		];
	}
}