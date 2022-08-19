<?php

namespace Clever\Library\Game;

use Clever\Library\Config;
use Clever\Library\Database;
use Clever\Library\Encryption;

use Workerman\Worker;
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
	 * Clever\Library\Database
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

		$this->database = new Database($this->config);
		$this->database->timeoutError = false;

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
		if (!isset($_GET['token'], $_COOKIE['serial'], $_COOKIE['game_id'])) {

			$connection->destroy();
			return;
		}

		$gameID = $this->crypto->decryptString($_COOKIE['game_id']);

		$gameData = $this->gameDB->preGameDataByID($gameID);

		if (!$gameData) {
			
			$connection->destroy();
			return;
		}

		switch ($gameData->type) {
			case 3:
				
				$result = $this->SoloHandler->newConnection($connection, $gameData);

			break;
			case 2:

				$result = $this->FriendHandler->newConnection($connection, $gameData);
				
			break;
			default:
				$connection->destroy();
				return;
			break;


		}
	}


	/**
	 * Function on message.
	 * 
	 * @return void
	 */
	public function onMessage($connection, $data)
	{

	}


	/**
	 * Function on connection closing.
	 * 
	 * @return void
	 */
	public function onClose($connection)
	{
		
	}
}