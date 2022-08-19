<?php 

namespace Clever\Library\Model;

use Clever\Library\Database;

class GamePlayers
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
	 * Insert new player in game.
	 * 
	 * @param int $userID
	 * @param int $gameID
	 * @param string $playerToken
	 * 
	 * @return void
	 */
	public function addPlayer($userID, $gameID, $playerToken)
	{
		$this->db->query("INSERT INTO game_players (id_user, id_game, token_player) VALUES (:id_user, :id_game, :token_player)",
			[
				'id_user' => $userID,
				'id_game' => $gameID,
				'token_player' => $playerToken,
			]
		);
	}


	/**
	 * Marks the start of a game for a specific player.
	 *
	 * @param int $recordID
	 * 
	 * @return void
	 */
	public function inGame($recordID)
	{
		$this->db->query("UPDATE game_players SET token_player = null WHERE id = :recordID", ['recordID' => $recordID]);
	}


	/**
	 * Find player by game ID and plyaer ID.
	 * 
	 * @param int $userID
	 * @param int $gameID
	 * 
	 * @return array
	 */
	public function playerDataByIDs($userID, $gameID)
	{
		return $this->db->query("SELECT * FROM game_players", ['id_user' => $userID, 'id_game' => $gameID])->fetch();
	}
}