<?php

namespace Clever\Library\Game;

use Clever\Library\Database;

use Workerman\Connection\TcpConnection;

use Clever\Library\Model\User;
use Clever\Library\Model\PersistantLogin;
use Clever\Library\Model\Game;
use Clever\Library\Model\GamePlayers;

class SoloHandler
{
	/**
	 * Clever\Library\Model\User
	 * 
	 * @var User
	 */
	private $userDB;


	/**
	 * Clever\Library\Model\PersistantLogin
	 * 
	 * @var PersistantLogin
	 */
	private $persistantLoginDB;


	/**
	 * Clever\Library\Model\Game
	 * 
	 * @var Game
	 */
	private $gameDB;


	/**
	 * Clever\Library\Model\GamePlayers
	 * 
	 * @var GamePlayers
	 */
	private $gamePlayersDB;


	/**
	 * @param Clever\Library\Database $database
	 * 
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->userDB = new User($database);
		$this->persistantLoginDB = new PersistantLogin($database);

		$this->gameDB = new Game($database);
		$this->gamePlayersDB = new GamePlayers($database);
	}


	/**
	 * Creates a new game if the user has an assigned game.
	 * Return false for no game, true for game found but no message, array for game found and a message to send.
	 * 
	 * @param Workerman\Connection\TcpConnection $connection
	 * @param array $gameData
	 * 
	 * @return bool|array
	 */
	public function newConnection(TcpConnection $connection, $gameData)
	{
		$userData = $this->persistantLoginDB->findSerial($_COOKIE['serial']);

		if (!$userData) return false;

		$playerData = $this->gamePlayersDB->playerDataByIDs($userData->id, $gameData->id);

		if (!$playerData || $playerData->token_player != $_GET['token']) return false;

		$this->gameDB->inGame($gameData->id);
		$this->gamePlayersDB->inGame($playerData->id);

		$player = new Player($playerData);

		$game = new SoloGame($player, $gameData);
		

	}
}