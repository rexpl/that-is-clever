<?php

namespace Clever\Library\Controller\Login;

use Mexenus\Database\Database;

use Clever\Library\Encryption;
use Clever\Library\Config;
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

		$user = new User($database);
		$user = $user->find($_POST['username'], '= BINARY', 'username');

		if (!$user || !password_verify($_POST['email'], $user->mail_hash)) {

			return [
				'success' => false,
				'message' => t('reset_password_no_match'),
			];
		}


		$passwordMail = new PasswordResetMail($database);

		
		$lastMail = $passwordMail->select(['send_time'])
			->where('id_user', $user->id)
			->orderBy('send_time', 'DESC')
			->limit(1)
			->first();

		if ($lastMail && (time() - strtotime($lastMail->send_time)) < 300) {

			return [
				'success' => false,
				'message' => t('reset_password_wait_time'),
			];
		}


		$mail = $passwordMail->new();
		$mail->id_user = $user->id;


		/**
		 * We save the mail in db.
		 */
		while (true) {

			//serial has to be unique
			$mail->serial = Helper::randomString(64);
			if (!$passwordMail->serialExist($mail->serial)) break;
		}


		if ($lastMail) {

			/**
			 * We disable the previous emails.
			 */
			$passwordMail->update(['serial' => null, 'token' => null])
				->where('id_user', $user->id)
				->execute();
		}


		$crypto = new Encryption($config->get('ext_key'));
		/**
		 * Variables needed in the email:
		 */
		$email = $crypto->encryptString($_POST['email']);
		$token = Helper::randomString(64);
		$serial = $mail->serial;

		$mail->token = password_hash($token, PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);

		$mail->save();

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

		$passwordMail = new PasswordResetMail($database);
		$mail = $passwordMail->find($_POST['serial'], 'serial');

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

		$user = new User($database);
		$user = $user->find($mail->id_user);

		if (!password_verify($email, $user->mail_hash)) {

			return [
				'success' => false,
				'message' => 'Email address does not match. (Error: E1013)',
			];
		}

		$passwordMail->update(['serial' => null, 'token' => null, 'used_time' => date("Y-m-d H:i:s")])
			->where('id', $mail->id)
			->execute();

		$cryptoPersonnal = new Encryption();

		$user->password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => $config->get('bcrypt')]);
		$user->protected_key = $cryptoPersonnal->makeProtectedKey($_POST['password']);
		$user->mail = $cryptoPersonnal->encryptString($email);

		$user->save();
		
		return [
			'success' => true,
			'username' => $user->username,
		];
	}
}