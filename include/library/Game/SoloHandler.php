<?php

namespace Clever\Library\Game;

use Mexenus\Database\Database;

use Workerman\Connection\TcpConnection;

use Clever\Library\Model\User;
use Clever\Library\Model\PersistantLogin;
use Clever\Library\Model\GamePlayers;

use Clever\Library\Game\Gameplay\SoloGame;

class SoloHandler
{
	/**
	 * Clever\Library\Model\PersistantLogin
	 * 
	 * @var PersistantLogin
	 */
	private $persistantLogin;


	/**
	 * Clever\Library\Model\GamePlayers
	 * 
	 * @var GamePlayers
	 */
	private $gamePlayers;


	/**
	 * @param Clever\Library\Database $database
	 * 
	 * @return void
	 */
	public function __construct(Database $database)
	{
		$this->persistantLogin = new PersistantLogin($database);

		$this->gamePlayers = new GamePlayers($database);
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
	public function newConnection(TcpConnection $connection, $game)
	{
		$userID = $this->persistantLogin->find($_COOKIE['serial'], 'serial');

		if (!$userID) {
			
			$connection->destroy();
			return;
		}

		$player = $this->gamePlayers->select()
			->where('id_user', $userID->id_user)
			->where('id_game', $game->id)
			->first();

		if (!$player || $player->token_player != $_GET['token']) {

			$connection->destroy();
			return;
		}

		$game->status = 4;
		$game->save();

		$player->token_player = null;
		$player->save();

		return new SoloGame($connection, $game, $player);
	}
}