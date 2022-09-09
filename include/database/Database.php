<?php 

namespace Mexenus\Database;

use PDO;
use Exception;

use Mexenus\Database\Tools\DetectsLostConnections;

class Database
{
	use DetectsLostConnections;


	/**
	 * @var PDO
	 */
	private $connection;


	/**
	 * @var bool
	 */
	private $isConnected = false;


	/**
	 * @var bool
	 */
	public $logLastQueryTime = false;


	/**
	 * @var int
	 */
	private $lastQuery = null;


	/**
	 * Mysql host.
	 * 
	 * @var string
	 */
	private $host;


	/**
	 * Database name.
	 * 
	 * @var string
	 */
	private $name;


	/**
	 * Mysql user.
	 * 
	 * @var string
	 */
	private $user;


	/**
	 * Mysql password.
	 * 
	 * @var string
	 */
	private $pass;

	
	/**
	 * @param string $host
	 * @param string $name
	 * @param string $user
	 * @param string $pass
	 * 
	 * @return void
	 */
	public function __construct($host, $name, $user, $pass)
	{
		$this->host = $host;
		$this->name = $name;
		$this->user = $user;
		$this->pass = $pass;
	}


	/**
	 * Initiate the connection.
	 * 
	 * @return void
	 */
	private function init()
	{
		$this->connection = new PDO('mysql:host='.$this->host.';dbname='.$this->name.';charset=utf8',
			$this->user,
			$this->pass,
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);

		$this->isConnected = true;
	}


	/**
	 * Connect to mysql server.
	 * 
	 * @return mixed
	 */
	private function connect()
	{
		
		try {

			return $this->init();

		} catch (Exception $e) {

			if ($this->causedByLostConnection($e)) {

				return $this->init();
			}

			/**
			 * Other error we let die.
			 */
			throw $e;
		}
	}


	/**
	 * initiate the connection if not already done.
	 * 
	 * @return void
	 */
	private function connectIfNotConnected()
	{
		if (!$this->isConnected) return $this->connect();
	}


	/**
	 * Execute the requested statement.
	 * 
	 * @param string $sql
	 * @param array $param
	 * 
	 * @return mixed
	 */
	private function statement($sql, $param = null)
	{
		if ($this->logLastQueryTime) $this->lastQuery = time();

		if (!$param) return $this->connection->query($sql);

		$req = $this->connection->prepare($sql);
		$req->execute($param);

		return $req;
	}


	/**
	 * Reinitiate the connection. And retry the query.
	 * Exeception is not caught.
	 *
	 * @param string $sql
	 * @param array $param
	 * 
	 * @return mixed
	 */
	private function tryAgainOnLostConnection($sql, $param)
	{
		$this->connect();

		return $this->statement($sql, $param);
	}


	/**
	 * Query to be made.
	 *
	 * @param string $sql
	 * @param array $param
	 * 
	 * @return mixed
	 */
	public function query($sql, $param = null)
	{
		$this->connectIfNotConnected();

		try {

			return $this->statement($sql, $param);

		} catch (Exception $e) {

			if ($this->causedByLostConnection($e)) {

				return $this->tryAgainOnLostConnection($sql, $param);
			}

			/**
			 * Other error we let die.
			 */
			throw $e;

		}
	}


	/**
	 * Return last insert ID.
	 * 
	 * We connect even if we did not execute a query so that the user
	 * receives a proper error message from PDO instead of undefined method.
	 * 
	 * @return int
	 */
	public function lastInsertId()
	{
		$this->connectIfNotConnected();

		return $this->connection->lastInsertId();
	}


	/**
	 * Return the pdo instance.
	 * 
	 * @return PDO
	 */
	public function pdo()
	{
		$this->connectIfNotConnected();
		
		return $this->connection;
	}


	/**
	 * Calculate number of seconds since last query.
	 * 
	 * @return mixed
	 */
	public function secondsOfInactivity()
	{ 
		if (!$this->isConnected || !$this->logLastQueryTime) return false;

		if (!$this->lastQuery) return null;

		return time() - $this->lastQuery;
	}


	/**
	 * Close the pdo connection without destroying the class.
	 * The connection will open on the first interaction with this object.
	 * 
	 * @return bool
	 */
	public function sleep()
	{
		if (!$this->isConnected) return false;

		$this->connection = null;
		$this->isConnected = false;

		return true;
	}
}