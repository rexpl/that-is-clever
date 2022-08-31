<?php

namespace Clever\Library\Controller\PreGame;

use Mexenus\Database\Database;

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
	 * @param Mexenus\Database\Database
	 * @param Clever\Library\Config
	 * 
	 * @return callable
	 */
	public function create(Database $database, Config $config)
	{
		if (!isset($_GET['q']) || !in_array($_GET['q'], ['solo', 'friend'])) {

			return [
				'success' => false,
				'message' => 'Missing GET parameter. (Error: E1015)',
			];
		}

		if ($_GET['q'] == 'solo') return $this->solo($database, $config);

		return $this->friend($database, $config);
	}


	/**
	 * Create solo game.
	 * 
	 * @param Mexenus\Database\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	private function solo(Database $database, Config $config) {

		$game = new Game($database);
		$game = $game->new();

		$game->type = 3;

		$game->save();

		$player = new GamePlayers($database);
		$player = $player->new();

		$player->id_user = $_SESSION['id_user'];
		$player->id_game = $game->id;
		$player->token_player = Helper::randomString(128);

		$player->save();

		$_SESSION['game_id'] = $game->id;
		$_SESSION['game_type'] = 'solo';
		$_SESSION['game_token'] = $player->token_player;

		$crypto = new Encryption($config->get('ext_key'));

		setcookie("game_id", $crypto->encryptString($game->id), time()+100, "/", "", $config->get('cookie_secure'), true);

		return [
			'success' => true,
		];
	}


	/**
	 * Create friend game.
	 * 
	 * @param Mexenus\Database\Database
	 * @param Clever\Library\Config
	 * 
	 * @return array
	 */
	private function friend(Database $database, Config $config) {

		$gameDB = new Game($database);

		$game = $gameDb->new();

		while (true) {

			//token has to be unique.
			$game->token = strtoupper(Helper::randomString(8));
			if (!$gameDB->tokenExist($game->token)) break;
		}

		$game->type = 2;

		$game->save();

		$player = new GamePlayers($database);
		$player = $player->new();

		$player->id_user = $_SESSION['id_user'];
		$player->id_game = $game->id;
		$player->token_player = Helper::randomString(128);

		$player->save();

		$_SESSION['game_id'] = $game->id;
		$_SESSION['game_type'] = 'friend';
		$_SESSION['game_token'] = $player->token_player;

		$crypto = new Encryption($config->get('ext_key'));

		setcookie("game_id", $crypto->encryptString($game->id), time()+100, "/", "", $config->get('cookie_secure'), true);

		return [
			'success' => true,
			'token' => $game->token,
		];
	}
}