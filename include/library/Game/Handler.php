<?php

namespace Clever\Library\Game;

use Mexenus\Database\Database;

use Clever\Library\Config;
use Clever\Library\Encryption;

use Workerman\Worker;
use Workerman\Timer;
use Workerman\Connection\TcpConnection;

use Clever\Library\Model\Game;

class Handler
{
	/**
	 * Clever\Library\Config
	 * 
	 * @var Config
	 */
	private $config;


	/**
	 * Mexenus\Database\Database
	 * 
	 * @var Database
	 */
	private $database;


	/**
	 * Clever\Library\Encryption
	 * 
	 * @var Encryption
	 */
	private $crypto;


	/**
	 * Clever\Library\Model\Game
	 * 
	 * @var Game
	 */
	private $gameDB;


	/**
	 * Workerman\Worker
	 * 
	 * @var Worker
	 */
	private $websocket;


	/**
	 * Clever\Library\Game\SoloHandler
	 * 
	 * @var SoloHandler
	 */
	private $SoloHandler;


	/**
	 * Clever\Library\Game\FriendHandler
	 * 
	 * @var FriendHandler
	 */
	private $FriendHandler;


	/**
	 * Contains all the game instances.
	 *  
	 * @var array
	 */
	private $instances = [];


	/**
	 * ID of the the timer used to clear the unused objects.
	 *  
	 * @var int
	 */
	private $timerID = null;


	/**
	 * @param Clever\Library\Config $config
	 * 
	 * @return void
	 */
	public function __construct(Config $config, Worker $websocket)
	{
		$this->config = $config;
		$this->websocket = $websocket;

		$this->crypto = new Encryption($config->get('ext_key'));
	}


	/**
	 * Function on worker start.
	 * 
	 * @return void
	 */
	public function onWorkerStart()
	{
		$this->file = tmpfile();
		$this->file2 = tmpfile();

		$data = "#!/usr/bin/env php\n" . file_get_contents('/var/www/html/that-is-clever/test.php');

		fwrite($this->file, $data);
		fwrite($this->file2, $data);

		$this->database = new Database($this->config->get('db_host'), $this->config->get('db_name'), $this->config->get('db_user'), $this->config->get('db_pass'));

		$this->gameDB = new Game($this->database);
		
		$this->SoloHandler = new SoloHandler($this->database);
		$this->FriendHandler = new FriendHandler($this->database);
	}


	/**
	 * Function on connection opening.
	 * 
	 * @param Workerman\Connection\TcpConnection
	 * 
	 * @return void
	 */
	public function onWebSocketConnect(TcpConnection $connection)
	{
		if (!$this->timerID) $this->timerID = Timer::add(3600, [$this, 'clearUnusedObjects']);


		if (isset($_GET['bot'])) return $this->incomingBotRequest($connection);


		if (!isset($_GET['token'], $_COOKIE['serial'], $_COOKIE['game_id'])) {

			$connection->destroy();
			return;
		}


		try {

			$gameID = $this->crypto->decryptString($_COOKIE['game_id']);

		} catch (\Exception $e) {

			$connection->destroy();
			return;
		}
		

		$game = $this->gameDB->find($gameID);

		if (!$game || $game->status != 2) {
			
			$connection->destroy();
			return;
		}

		switch ($game->type) {
			case 3:
				
				$object = $this->SoloHandler->newConnection($connection, $game);

			break;
			case 2:

				$object = $this->FriendHandler->newConnection($connection, $game);
				
			break;
			default:
				$connection->destroy();
				return;
			break;


		}

		$this->instances[] = $object;
	}


	private function incomingBotRequest(TcpConnection $connection)
	{
		
	}


	private function sleep()
	{
		Timer::del($this->timerID);
		$this->timerID = null;

		$this->instances = [];

		$this->database->sleep();
	}


	/**
	 * Garbage collector. Clear finished games.
	 * 
	 * @return void
	 */
	public function clearUnusedObjects()
	{
		if (empty($this->instances)) return $this->sleep();

		foreach ($this->instances as $key => $value) {
			
			if ($this->instances[$key]->destroy) unset($this->instances[$key]);
		}
	}
}