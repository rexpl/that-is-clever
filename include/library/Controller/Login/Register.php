<?php 

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

use Clever\Library\Encryption;
use Clever\Library\Config;
use Clever\Library\Helper;

use Clever\Library\Model\User;

class Register
{
	/**
	 * Try to register the user.
	 * 
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	public function register(Database $database, Config $config)
	{
		if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password_confirm']) || empty($_POST['mail'])) {

			return [
				'success' => false,
				'message' => t('login_fill_fields'),
			];
		}

		$user = new User($database);

		if ($user->usernameExist($_POST['username'])) {

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

		$user = $user->new();
		$user->username = $_POST['username'];

		$user->password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$user->mail_hash = password_hash($_POST['mail'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);

		$crypto = new Encryption();
		
		$user->protected_key = $crypto->makeProtectedKey($_POST['password']);
		$user->mail = $crypto->encryptString($_POST['mail']);

		$user->save();

		return [
			'success' => true,
		];
	}
}