<?php

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

use Clever\Library\Config;
use Clever\Library\Encryption;
use Clever\Library\Helper;

use Clever\Library\Model\User;


class UpdateCredentials
{
	/**
	 * Update the password.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\App\Config
	 * 
	 * @return array
	 */
	public function updatePassword(Database $database, Config $config)
	{
		
	}


	/**
	 * Update the email address.
	 *
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	public function updateEmail(Database $database, Config $config)
	{
		if ($_SESSION['cookie_login']) {

			return [
				'success' => false,
				'message' => 'key',
			];
		}

		if (empty($_POST['password']) || empty($_POST['email'])) {

			return [
				'success' => false,
				'message' => t('login_fill_fields'),
			];
		}

		if (!Helper::validEmail($_POST['email'])) {
			
			return [
				'success' => false,
				'message' => t('register_no_valid_mail'),
			];
		}

		$user = new User($database);
		$user = $user->find($_SESSION['id_user']);

		if (!password_verify($_POST['password'], $user->password)) {

			return [
				'success' => false,
				'message' => t('login_failed_attempt'),
			];
		}

		$crypto = new Encryption($_SESSION['personnal_key']);

		if ($_POST['email'] == $crypto->decryptString($user->mail)) {
			
			return [
				'success' => false,
				'message' => t('settings_nochange'),
			];
		}

		$user->mail = $crypto->encryptString($_POST['email']);
		$user->mail_hash = password_hash($_POST['email'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);

		$user->save();

		return [
			'success' => true,
			'message' => t('settings_mail_success'),
			'new_text' => sprintf(t('settings_mail_message'), e($_POST['email'])),
		];
	}
}