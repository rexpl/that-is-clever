<?php 

namespace Clever\Library\App\Model;

use Clever\Library\App\Database;

class PersistantLogin
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
	 * Get data to linked to serial.
	 *
	 * @param string $serial
	 * 
	 * @return array
	 */
	public function findSerial($serial)
	{
		return $this->db->query("SELECT id, id_user, token FROM persistent_login WHERE serial = :serial", ['serial' => $serial])->fetch();
	}


	/**
	 * Update the token.
	 *
	 * @param int $recordID
	 * @param string $token
	 * 
	 * @return void
	 */
	public function updateToken($recordID, $token)
	{
		$this->db->query("UPDATE persistent_login SET token = :token WHERE id = :id", ['token' => $token, 'id' => $recordID]);
	}


	/**
	 * Delete all record associated to user.
	 *
	 * @param int $userID
	 * 
	 * @return void
	 */
	public function deleteAllByUserID($userID)
	{
		$this->db->query("DELETE FROM persistent_login WHERE id_user = :id", ['id' => $userID]);
	}


	/**
	 * look if given serial already exist.
	 *
	 * @param string $serial
	 * 
	 * @return bool
	 */
	public function serialExist($serial)
	{
		return $this->db->query("SELECT 1 FROM persistent_login WHERE serial = :serial", ['serial' => $serial])->fetch();
	}



	/**
	 * Insert new login.
	 * 
	 * @param int $userID
	 * @param string $serial
	 * @param string $token
	 * 
	 * @return void
	 */
	public function new($userID, $serial, $token)
	{
		$this->db->query("INSERT INTO persistent_login (id_user, serial, token) VALUES (:id_user, :serial, :token)", ['id_user' => $userID, 'serial' => $serial, 'token' => $token]);
	}

}