<?php

namespace Clever\Library\App\Model;

use Clever\Library\App\Database;

class PasswordResetMail
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
	 * Get the last mail data by user ID.
	 *
	 * @param int $userID
	 * 
	 * @return array
	 */
	public function getLastMailByUserID($userID)
	{
		return $this->db->query("SELECT send_time FROM mail_password_reset WHERE id_user = :id_user ORDER BY send_time DESC LIMIT 1", ['id_user' => $userID])->fetch();
	}


	/**
	 * Get mail data by serial.
	 *
	 * @param string $serial
	 * 
	 * @return array
	 */
	public function getMailBySerial($serial)
	{
		return $this->db->query("SELECT * FROM mail_password_reset WHERE serial = :serial", ['serial' => $serial])->fetch();
	}


	/**
	 * Set mail as used by ID.
	 *
	 * @param string $serial
	 * 
	 * @return array
	 */
	public function setMailUsedByID($mailID)
	{
		return $this->db->query("UPDATE mail_password_reset SET serial = null, token = null, used_time = now() WHERE id = :mailID", ['mailID' => $mailID]);
	}


	/**
	 * Get the last mail data by user ID.
	 *
	 * @param int $userID
	 * 
	 * @return array
	 */
	public function disableAllSerialByUserID($userID)
	{
		return $this->db->query("UPDATE mail_password_reset SET serial = null, token = null WHERE id_user = :id_user", ['id_user' => $userID]);
	}


	/**
	 * Add new mail.
	 * 
	 * @param int $userID
	 * @param string $serial
	 * @param string $token
	 * 
	 * @return array
	 */
	public function newMail($userID, $serial, $token)
	{
		$this->db->query("INSERT INTO mail_password_reset (id_user, serial, token) VALUES (:id_user, :serial, :token)", ['id_user' => $userID, 'serial' => $serial, 'token' => $token]);
	}


	/**
	 * Look if given serial already exist.
	 *
	 * @param string $serial
	 * 
	 * @return bool
	 */
	public function serialExist($serial)
	{
		return $this->db->query("SELECT 1 FROM mail_password_reset WHERE serial = :serial", ['serial' => $serial])->fetch();
	}
}