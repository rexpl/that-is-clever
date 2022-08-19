<?php

namespace Clever\Library\Controller\PreGame;

use Clever\Library\Database;
use Clever\Library\Config;
use Clever\Library\Helper;
use Clever\Library\Encryption;

use Clever\Library\Model\Game;
use Clever\Library\Model\GamePlayers;

class Create
{
	/**
	 * Create game.
	 * 
	 * @param Clever\Library\Database
	 * 
	 * @return callable
	 */
	public static function create(Database $database, Config $config)
	{
		if (!isset($_GET['q']) || !in_array($_GET['q'], ['solo', 'friend'])) {

			return [
				'success' => false,
				'message' => 'Missing GET parameter. (Error: E1015)',
			];
		}

		if ($_GET['q'] == 'solo') return self::solo($database, $config);

		return self::friend($database, $config);
	}


	/**
	 * Create solo game.
	 * 
	 * @param Clever\Library\Database
	 * 
	 * @return array
	 */
	private static function solo(Database $database, Config $config) {

		$game = new Game($database);
		$gameID = $game->createGame();

		$playerToken = Helper::randomString(128);

		$gamePlayers = new GamePlayers($database);
		$gamePlayers->addPlayer($_SESSION['id_user'], $gameID, $playerToken);

		$_SESSION['game_id'] = $gameID;
		$_SESSION['game_type'] = 'solo';
		$_SESSION['game_token'] = $playerToken;

		$crypto = new Encryption($config->get('ext_key'));

		setcookie("game_id", $crypto->encryptString($gameID), time()+300, "/", "", $config->get('cookie_secure'), true);

		return [
			'success' => true,
			'gameID' => $gameID,
		];
	}


	/**
	 * Create friend game.
	 * 
	 * @param Clever\Library\Database
	 * 
	 * @return array
	 */
	private static function friend(Database $database, Config $config) {

		$game = new Game($database);

		while (true) {

			//token has to be unique.
			$token = strtoupper(Helper::randomString(8));
			if (!$game->tokenExist($token)) break;
		}

		$gameID = $game->createGame($token);

		$playerToken = Helper::randomString(128);

		$gamePlayers = new GamePlayers($database);
		$gamePlayers->addPlayer($_SESSION['id_user'], $gameID, $playerToken);

		$_SESSION['game_id'] = $gameID;
		$_SESSION['game_type'] = 'solo';
		$_SESSION['game_token'] = $playerToken;

		$crypto = new Encryption($config->get('ext_key'));

		setcookie("game_id", $crypto->encryptString($gameID), time()+300, "/", "", $config->get('cookie_secure'), true);

		return [
			'success' => true,
			'token' => $token,
		];
	}
}