<?php 

namespace Clever\Library;

use PDO;

use Clever\Library\Config;

class Database
{
	/**
	 * PDO
	 */
	private $connection;


	/**
	 * Clever\Library\Config
	 */
	private $config;


	/**
	 * Disable/Enable timeout error on query.
	 * Useful for the websocket because of regular timeouts.
	 * 
	 * The timeouts are corrected by try & catch.
	 */
	public $timeoutError = true;

	
	/**
	 * Initiate the connection.
	 *
	 * @param Clever\Library\Config
	 * 
	 * @return init()
	 */
	public function __construct(Config $config)
	{
		$this->config = $config;

		return $this->init();
	}


	/**
	 * Connect to mysql server.
	 * 
	 * @return void
	 */
	private function init()
	{
		$this->connection = new PDO('mysql:host='.$this->config->get('db_host').';dbname='.$this->config->get('db_name').';charset=utf8',
			$this->config->get('db_user'),
			$this->config->get('db_pass'),
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);
	}


	/**
	 * Execute the requested statement.
	 * 
	 * @param string $sql
	 * @param array $param
	 * 
	 * @return array|bool|string
	 */
	private function statement($sql, $param = null)
	{
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
	 * @return statement()
	 */
	private function tryAgainOnTimeOut($sql, $param)
	{
		$this->init();

		return $this->statement($sql, $param);	
	}


	/**
	 * Query to be made.
	 *
	 * @param string $sql
	 * @param array $param
	 * 
	 * @return statement()
	 */
	public function query($sql, $param = null)
	{
		try {

			if (!$this->timeoutError) {

				$errorReportingLevel = error_reporting(0);

				$result = $this->statement($sql, $param);

				error_reporting($errorReportingLevel);
			}
			else {

				$result = $this->statement($sql, $param);
			}

			return $result;

		} catch (PDOException $e) {

			/**
			 * Mysql server has gone away. 
			 */
			if (!$this->timeoutError && $e->getCode() == 2006) {

				return $this->tryAgainOnTimeOut($sql, $param);
			}

			/**
			 * Other error we let die.
			 */
			throw $e;
			die();

		}
	}


	/**
	 * Return last insert ID.
	 * 
	 * @return int
	 */
	public function lastInsertId()
	{
		return $this->connection->lastInsertId();
	}
}