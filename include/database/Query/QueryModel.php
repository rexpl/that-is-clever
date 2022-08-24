<?php

namespace Mexenus\Database\Query;

use Mexenus\Database\Database;

use Mexenus\Database\Tools\QueryStarter;
use Mexenus\Database\Query\Query;

use JsonSerializable;

class QueryModel implements JsonSerializable
{
	use QueryStarter;


	/**
	 * @var Database
	 */
	private $database;


	/**
	 * Contains the table name.
	 * 
	 * @var string
	 */
	private $table;


	/**
	 * Contains the primary key.
	 * 
	 * @var string
	 */
	private $primary;


	/**
	 * Contains the database values to hide on json_encode.
	 * 
	 * @var array
	 */
	private $hidden;


	/**
	 * Contains the database values.
	 * 
	 * @var array
	 */
	private $values;


	/**
	 * Contains the database values before boot.
	 * 
	 * @var array
	 */
	private $tmpValues;


	/**
	 * Contains the fields wich have been modified.
	 * 
	 * @var array
	 */
	private $modifiedFields = [];


	/**
	 * If needs insert on save.
	 * 
	 * @var bool
	 */
	private $new;


	/**
	 * If construct has been called.
	 * 
	 * @var bool
	 */
	private $isBooted = false;


	/**
	 * If a variable has been modified.
	 * 
	 * @var bool
	 */
	private $isDirty = false;


	/**
	 * @param Database $database
	 * @param string $table
	 * @param array $values
	 * 
	 * @return void
	 */
	public function __construct(Database $database, $table, $primary, array $hidden, array $values = [], $new = false)
	{
		$this->database = $database;

		$this->table = $table;
		$this->primary = $primary;

		$this->hidden = $hidden;

		$this->boot($values, $new);
	}


	/**
	 * Boots the class, useful because construct is called after the properties are set.
	 * 
	 * @return void
	 */
	private function boot($values, $new)
	{
		if ($new) {

			$this->values = $values;
			$this->new = true;
		}
		else {

			$this->values = $this->tmpValues;
		}

		$this->isBooted = true;
	}


	/**
	 * Magic method to access db fields.
	 * 
	 * @param string $name
	 * 
	 * @return mixed
	 */
	public function __get($name)
	{
		if (array_key_exists($name, $this->values)) {
			
			return $this->values[$name];
		}

		return null;
	}


	/**
	 * __set before the class is booted.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * 
	 * @return void
	 */
	private function setVarBeforeBoot($name, $value)
	{
		$this->tmpValues[$name] = $value;
	}


	/**
	 * __set after the class is booted.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * 
	 * @return void
	 */
	private function setVarAfterBoot($name, $value)
	{
		if (!$this->new) {

			$this->modifiedFields[$name] = $value;
			$this->isDirty = true;
		}

		$this->values[$name] = $value;
	}


	/**
	 * Magic method to keep track of new and modified fields.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * 
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		if ($this->isBooted) return $this->setVarAfterBoot($name, $value);

		return $this->setVarBeforeBoot($name, $value);
	}


	/**
	 * New Query() instance.
	 * 
	 * @return Mexenus\Database\Query
	 */
	private function newQuery()
	{
		$query = new Query($this->database);

		$query->table = $this->table;

		$query->primary = $this->primary;

		$query->hidden = $this->hidden;

		return $query;
	}


	/**
	 * Called upon save() if new = true
	 * 
	 * @return void
	 */
	private function insertOnSave()
	{
		$this->insert()->values($this->values, false);

		if ($this->primary == 'id') $this->values['id'] = $this->database->lastInsertId();
		$this->new = false;
	}


	/**
	 * Called upon save() if new = false
	 * 
	 * @return void
	 */
	private function updateOnSave()
	{
		if (!$this->isDirty) return;

		$this->update($this->modifiedFields)->where($this->primary, $this->values[$this->primary])->execute();

		$this->modifiedFields = [];
		$this->isDirty = false;
	}


	/**
	 * This allow to easily update/insert in the column.
	 * 
	 * @return mixed
	 */
	public function save()
	{
		if ($this->new) return $this->insertOnSave();

		return $this->updateOnSave();
	}


	/**
	 * If the values have been changed since the last query.
	 * 
	 * @return bool
	 */
	public function isDirty()
	{
		return $this->isDirty;
	}


	/**
	 * Delete the record if not new.
	 * 
	 * @return void
	 */
	public function del()
	{
		if ($this->new) return;

		$this->delete()->where($this->primary, $this->values[$this->primary])->execute();
	}


	/**
	 * Refresh the record from the database.
	 * 
	 * @return void
	 */
	public function refresh()
	{
		if ($this->new) return;

		
	}


	/**
	 * Change the behavior of json_encode()
	 * 
	 * @return array
	 */
	public function jsonSerialize()
	{
		if (empty($this->hidden)) return $this->values;

		$result = [];

		foreach ($this->values as $key => $value) {
			
			if (in_array($key, $this->hidden)) continue;

			$result[$key] = $value;
		}

		return $result;
	}
}