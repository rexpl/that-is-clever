<?php

namespace Clever\Library;

use Clever\Library\Database;

abstract class Model
{
	protected $database;


	public function __construct(Database $database)
	{
		$this->database = $database;
	}


	
}