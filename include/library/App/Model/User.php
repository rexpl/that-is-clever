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
	 * @return username
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
	 * @return id, password, protected_key, failed_login_count
	 */
	public function getLoginDataByUsername($username)
	{
		return $this->db->query("SELECT id, password, protected_key, failed_login_count FROM user WHERE username = BINARY :username AND status = 1", ['username' => $username])->fetch();
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
		$email = $this->db->query("SELECT mail FROM user WHERE id = :id", ['id' => $userID])->fetch()->username;

		return $crypto->decrypt($email);
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
	 * verify if the user exist or not.
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
	 * @return vois
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
}