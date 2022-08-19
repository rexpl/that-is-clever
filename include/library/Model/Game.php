<?php 

namespace Clever\Library\Model;

use Clever\Library\Database;

class Game
{
	private $db;


	/**
	 * @param Clever\Library\Database
	 * 
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->db = $database;
	}


	/**
	 * Look if given token already exist for freidns game.
	 *
	 * @param string $token
	 * 
	 * @return bool
	 */
	public function tokenExist($token)
	{
		return $this->db->query("SELECT 1 FROM game WHERE type = 3 AND token = :token", ['token' => $token])->fetch();
	}


	/**
	 * Get a game wich hasn't started yet by it's ID.
	 *
	 * @param int $gameID
	 * 
	 * @return array
	 */
	public function preGameDataByID($gameID)
	{
		return $this->db->query("SELECT * FROM game WHERE id = :gameID AND status = 2", ['gameID' => $gameID])->fetch();
	}


	/**
	 * Marks the start of a game.
	 *
	 * @param int $gameID
	 * 
	 * @return void
	 */
	public function inGame($gameID)
	{
		$this->db->query("UPDATE game SET status = 4 WHERE id = :gameID", ['gameID' => $gameID]);
	}


	/**
	 * Create a new game. And return game ID.
	 *
	 * @param string $token
	 * 
	 * @return int
	 */
	public function createGame($token = null)
	{
		if (is_null($token)) {
			$this->db->query("INSERT INTO game (type, token) VALUES (3, :token)", ['token' => $token]);
		}
		else {
			$this->db->query("INSERT INTO game (type) VALUES (2)");
		}

		return $this->db->lastInsertId();
	}
}