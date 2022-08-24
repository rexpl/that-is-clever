<?php

namespace Mexenus\Database\Query;

use PDO;

use Mexenus\Database\Query\QueryModel;

abstract class QueryBuilder
{
	/**
	 * Return the parameters.
	 * 
	 * @return array
	 */
	public function returnParam()
	{
		return $this->query->param;
	}


	/**
	 * Return the query as string.
	 * 
	 * @return string
	 */
	public function returnQuery()
	{
		return $this->query->query;
	}


	/**
	 * Return the query and parameters.
	 * 
	 * @return array
	 */
	public function return()
	{
		return [
			'query' => $this->query->query,
			'param' => $this->query->param,
		];
	}


	public function first()
	{
		return $this->execSelect(true);
	}


	public function get()
	{
		return $this->execSelect(false);
	}


	/**
	 * Execute the select statement.
	 * 
	 * @return mixed
	 */
	public function execSelect($firstResultOnly)
	{
		$result = $this->query->database->query($this->query->query, $this->query->param)
			->fetchAll(PDO::FETCH_CLASS, QueryModel::class, [
				$this->query->database,
				$this->query->table,
				$this->query->primary,
				$this->query->hidden,
			]
		);

		if (empty($result)) return false;
		
		if ($firstResultOnly) return $result[0];

		return $result;
	}


	/**
	 * Execute the statement.
	 * 
	 * @return mixed
	 */
	public function execute()
	{
		return $this->query->database->query($this->query->query, $this->query->param);
	}


	/**
	 * Return the parameters.
	 * 
	 * @return array
	 */
	public function prepare()
	{
		
	}
}