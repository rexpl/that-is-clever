<?php 

namespace Clever\Library\App;

use Clever\Library\App\Encryption;
use Clever\Library\App\Config;
use Clever\Library\App\Database;
use Clever\Library\App\Helper;

use Clever\Library\App\Model\User;

class Register
{
	/**
	 * Verify if username already in use. This can be an ajax response.
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
	 * Try to register the user. This an ajax response.
	 * 
	 * @param Clever\Library\App\Database
	 * @param Clever\Library\App\Config
	 * 
	 * @return array
	 */
	public static function register(Database $database, Config $config)
	{
		if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password_confirm']) || empty($_POST['mail'])) {

			return [
				'success' => false,
				'message' => t('login_fill_fields'),
			];
		}

		if (self::username($database, $_POST['username'])) {

			return [
				'success' => false,
				'message' => t('register_username_match'),
			];
		}

		if (!Helper::validPassword($_POST['password']) || $_POST['password'] != $_POST['password_confirm']) {

			return [
				'success' => false,
				'message' => t('register_password_not_valid'),
			];
		}

		if (!Helper::validEmail($_POST['mail'])) {
			
			return [
				'success' => false,
				'message' => t('register_no_valid_mail'),
			];
		}

		$crypto = new Encryption();
		$password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$mail_hash = password_hash($_POST['mail'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$protected_key = $crypto->makeProtectedKey($_POST['password']);
		$mail = $crypto->encryptString($_POST['mail']);

		$user = new User($database);
		$user->createUser($_POST['username'], $password, $mail_hash, $protected_key, $mail);

		return [
			'success' => true,
		];
	}
}