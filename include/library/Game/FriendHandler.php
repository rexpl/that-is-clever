<?php

namespace Clever\Library\Game;

use Mexenus\Database\Database;

use Workerman\Connection\TcpConnection;

use Clever\Library\Model\User;
use Clever\Library\Model\PersistantLogin;
use Clever\Library\Model\Game;
use Clever\Library\Model\GamePlayers;

class FriendHandler
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
	 * Return false for no game, true for game but no message, array for game and message to send.
	 * 
	 * @param Workerman\Connection\TcpConnection $connection
	 * @param array $gameData
	 * 
	 * @return bool|array
	 */
	public function newConnection(TcpConnection $connection, $gameData)
	{

	}
}