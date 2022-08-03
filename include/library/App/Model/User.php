<?php 

namespace Clever\Library\App\Model;

use Clever\Library\App\Database;
use Clever\Library\App\Encryption;

class User
{
	private $db;


	/**
	 * @param Clever\Library\App\Database
	 * 
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->db = $database;
	}


	/**
	 * Get the username by ID.
	 *
	 * @param int $userID
	 * 
	 * @return string
	 */
	public function getUsernameByID($userID)
	{
		return $this->db->query("SELECT username FROM user WHERE id = :id_user", ['id_user' => $userID])->fetch()->username;
	}


	/**
	 * Get the id, password, protected_key, failed_login_count by username.
	 *
	 * @param string $username
	 * 
	 * @return array
	 */
	public function getLoginDataByUsername($username)
	{
		return $this->db->query("SELECT id, password, protected_key, failed_login_count FROM user WHERE username = BINARY :username AND status = 1", ['username' => $username])->fetch();
	}


	/**
	 * Get the password, protected_key by id.
	 *
	 * @param int $userID
	 * 
	 * @return array
	 */
	public function getLoginDataByID($userID)
	{
		return $this->db->query("SELECT password, protected_key FROM user WHERE id = :id AND status = 1", ['id' => $userID])->fetch();
	}


	/**
	 * Get the all necessary data for a password reset.
	 *
	 * @param int $userID
	 * 
	 * @return string
	 */
	public function getMailHashByID($userID)
	{
		return $this->db->query("SELECT mail_hash FROM user WHERE id = :id", ['id' => $userID])->fetch()->mail_hash;
	}


	/**
	 * Get the decrypted email address by ID.
	 *
	 * @param Clever\Library\App\Encryption
	 * @param int $userID
	 * 
	 * @return string
	 */
	public function getMailByID(Encryption $crypto, $userID)
	{
		$email = $this->db->query("SELECT mail FROM user WHERE id = :id", ['id' => $userID])->fetch()->mail;

		return $crypto->decryptString($email);
	}


	/**
	 * Get the mail hash by ursername for password reset.
	 * 
	 * @param string $username
	 * 
	 * @return void
	 */
	public function getMailHashByUsername($username)
	{
		return $this->db->query("SELECT id, mail_hash FROM user WHERE username = BINARY :username", ['username' => $username])->fetch();
	}


	/**
	 * Update the email adrres of a user by ID
	 *
	 * @param Clever\Library\App\Encryption
	 * @param string $mail_hash
	 * @param string $email
	 * @param int $userID
	 * 
	 * @return void
	 */
	public function updateMailByID(Encryption $crypto, $mail_hash, $email, $userID)
	{
		$email = $crypto->encryptString($email);

		$this->db->query("UPDATE user SET mail_hash = :mail_hash, mail = :mail WHERE id = :id", ['mail_hash' => $mail_hash, 'mail' => $email, 'id' => $userID]);
	}


	/**
	 * Update the password and protected key by ID
	 * 
	 * @param string $password
	 * @param int $userID
	 * 
	 * @return void
	 */
	public function updatePasswordByID($password, $key, $userID)
	{
		$this->db->query("UPDATE user SET password = :password, protected_key = :protected_key WHERE id = :id", ['password' => $password, 'protected_key' => $key, 'id' => $userID]);
	}


	/**
	 * Increment the failed_login counter..
	 *
	 * @param int $userID
	 * 
	 * @return void
	 */
	public function incrementFailedLoginCountByID($userID)
	{
		$this->db->query("UPDATE user SET failed_login_count = failed_login_count + 1 WHERE id = :id", ['id' => $userID]);
	}


	/**
	 * Verify if the user exist or not.
	 * 
	 * @param string $username
	 * 
	 * @return bool
	 */
	public function usernameExist($username)
	{
		return $this->db->query("SELECT 1 FROM user WHERE username = :username", ['username' => $username])->fetch();
	}


	/**
	 * Create new user.
	 * 
	 * @param string $username
	 * @param string $password
	 * @param string $mail_hash
	 * @param string $protected_key
	 * @param string $mail
	 * 
	 * @return void
	 */
	public function createUser($username, $password, $mail_hash, $protected_key, $mail)
	{
		return $this->db->query("
			INSERT INTO 
				user (status, username, password, mail_hash, protected_key, mail, failed_login_count)
			VALUES
				(1, :username, :password, :mail_hash, :protected_key, :mail, 0)",
			[
				'username' => $username,
				'password' => $password,
				'mail_hash' => $mail_hash,
				'protected_key' => $protected_key,
				'mail' => $mail,
			]
		);
	}


	/**
	 * reset password.
	 * 
	 * @param string $password
	 * @param string $mail_hash
	 * @param string $protected_key
	 * @param string $mail
	 * @param int $userID
	 * 
	 * @return void
	 */
	public function resetPasswordByID($password, $mail_hash, $protected_key, $mail, $userID)
	{
		return $this->db->query("
			UPDATE 
				user
			SET
				password = :password, mail_hash = :mail_hash, protected_key = :protected_key, mail = :mail, failed_login_count = 0
			WHERE
				id = :id_user",
			[
				'id_user' => $userID,
				'password' => $password,
				'mail_hash' => $mail_hash,
				'protected_key' => $protected_key,
				'mail' => $mail,
			]
		);
	}
}