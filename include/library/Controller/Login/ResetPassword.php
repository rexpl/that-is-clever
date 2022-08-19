<?php

namespace Clever\Library\Controller\Login;

use Clever\Library\Encryption;
use Clever\Library\Config;
use Clever\Library\Database;
use Clever\Library\Helper;
use Clever\Library\Mail;

use Clever\Library\Model\User;
use Clever\Library\Model\PasswordResetMail;

class ResetPassword
{
	/**
	 * Try to request a password reset.
	 * 
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	public function passwordMail(Database $database, Config $config)
	{
		if (empty($_POST['username']) || empty($_POST['email'])) {

			return [
				'success' => false,
				'message' => t('login_fill_fields'),
			];
		}

		$userDB = new User($database);
		$user = $userDB->getMailHashByUsername($_POST['username']);

		if (!$user || !password_verify($_POST['email'], $user->mail_hash)) {

			return [
				'success' => false,
				'message' => t('reset_password_no_match'),
			];
		}

		$crypto = new Encryption($config->get('ext_key'));
		$email = $crypto->encryptString($_POST['email']);
		$token = Helper::randomString(64);

		$mail = new PasswordResetMail($database);

		
		$lastMail = $mail->getLastMailByUserID($user->id);


		if ((time() - strtotime($lastMail->send_time)) < 300) {

			return [
				'success' => false,
				'message' => t('reset_password_wait_time'),
			];
		}


		/**
		 * We save the mail in db.
		 */
		while (true) {

			//serial has to be unique
			$serial = Helper::randomString(64);
			if (!$mail->serialExist($serial)) break;
		}

		$mail->disableAllSerialByUserID($user->id);
		$mail->newMail($user->id, $serial, password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]));

		require dirname(__DIR__, 3) .'/views/mail_password_reset.php';

		$mail = new Mail($config);

		$mail->addRecipient($_POST['email']);

		$mail->subject = t('login_reset_password');
		$mail->body = $body;

		$mail->send();

		return [
			'success' => true,
			'message' => t('reset_password_match'),
		];
	}


	/**
	 * Try to request a password reset.
	 * 
	 * @param Clever\Library\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	public function passwordReset(Database $database, Config $config)
	{
		if (empty($_POST['password']) || empty($_POST['password_confirm'])) {

			return [
				'success' => false,
				'message' => t('login_fill_fields'),
			];
		}

		if (empty($_POST['serial']) || empty($_POST['token']) || empty($_POST['email'])) {

			return [
				'success' => false,
				'message' => 'One or more hidden parameters are missing. (Error: E1012)',
			];
		}

		$mailDB = new PasswordResetMail($database);
		$mail = $mailDB->getMailBySerial($_POST['serial']);

		if (!$mail || (time() - strtotime($mail->send_time)) > 3600 || !password_verify($_POST['token'], $mail->token)) {

			return [
				'success' => false,
				'message' => t('reset_password_expired'),
			];
		}

		if (!Helper::validPassword($_POST['password'])) {

			return [
				'success' => false,
				'message' => t('register_password_not_valid'),
			];
		}

		if ($_POST['password'] != $_POST['password_confirm']) {

			return [
				'success' => false,
				'message' => t('register_password_no_match'),
			];
		}

		$cryptoExternal = new Encryption($config->get('ext_key'));
		$email = $cryptoExternal->decryptString($_POST['email']);

		$userDB = new User($database);
		if (!password_verify($email, $userDB->getMailHashByID($mail->id_user))) {

			return [
				'success' => false,
				'message' => 'Email address does not match. (Error: E1013)',
			];
		}

		$mailDB->setMailUsedByID($mail->id);

		$cryptoPersonnal = new Encryption();

		$password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$protected_key = $cryptoPersonnal->makeProtectedKey($_POST['password']);
		$mail_hash = password_hash($email, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$email = $cryptoPersonnal->encryptString($email);

		$userDB->resetPasswordByID($password, $mail_hash, $protected_key, $email, $mail->id_user);
		
		return [
			'success' => true,
			'username' => $userDB->getUsernameByID($mail->id_user),
		];
	}
}