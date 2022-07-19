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
	 * Get the decrypted email address by ID.
	 *
	 * @param Clever\Library\App\Encryption
	 * @param int $userID
	 * 
	 * @return string
	 */
	public function getMailByID(Encryption $crypto, $userID)
	{
		$email = $this->db->query("SELECT mail FROM user WHERE id = :id_user", ['id_user' => $userID])->fetch()->username;

		return $crypto->decrypt($email);
	}



}