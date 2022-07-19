<?php 

namespace Clever\Library\App;

use PDO;
use Clever\Library\App\Config;

class Database
{
	private $connection;
	
	/**
	 * Initiate the connection.
	 *
	 * @param Clever\Library\App\Config
	 * 
	 * @return void
	 */
	public function __construct(Config $config)
	{
		$this->connection = new PDO('mysql:host='.$config->get('db_host').';dbname='.$config->get('db_name').';charset=utf8',
			$config->get('db_user'),
			$config->get('db_pass'),
			[
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
				PDO::ATTR_EMULATE_PREPARES => false,
			]
		);
	}


	/**
	 * Query to be made.
	 *
	 * @param string
	 * @param array
	 * 
	 * @return query result (array)
	 */
	public function query($sql, $param = null)
	{
		if (!$param) return $this->connection->query($sql);

		$req = $this->connection->prepare($sql);
		$req->execute($param);

		return $req;
	}
}