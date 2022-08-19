<?php

namespace Mexenus\Database\Query;

use Mexenus\Database\Database;

class Query
{
	/**
	 * @var Mexenus\Database\Database
	 */
	public $database;


	/**
	 * Table name. Required.
	 * 
	 * @var string
	 */
	public $table;


	/**
	 * Table primary key. Default: id
	 * 
	 * @var string
	 */
	public $primary = 'id';


	/**
	 * Hidden fields on json_encode(). Default: []
	 * 
	 * @var array
	 */
	public $hidden = [];


	/**
	 * Default values for fields. Default: []
	 * 
	 * @var array
	 */
	public $default = [];


	/**
	 * The query currently being builded.
	 * 
	 * @var string
	 */
	public $query;


	/**
	 * Is select.
	 * 
	 * @var bool
	 */
	public $select = false;


	/**
	 * Count of the requested columns.
	 * 
	 * @var int
	 */
	public $selectColumnsCount = 0;


	/**
	 * The where has been positioned.
	 * 
	 * @var bool
	 */
	public $whereStart = false;


	/**
	 * params for pdo
	 * 
	 * @var array
	 */
	public $param = [];


	/**
	 * Counts the WHERE IN to associate the parameters.
	 * 
	 * @var int
	 */
	public $inCount = 0;


	/**
	 * If values has been set.
	 * 
	 * @var int
	 */
	public $insertValuesSet = false;


	public function __construct(Database $database)
	{
		$this->database = $database;
	}
}