<?php

namespace Clever\Library;

use Error;

class Config
{
	private $config;


	/**
	 * Load the config in $config.
	 *
	 * @param array $argument
	 * 
	 * @return void
	 */
	function __construct($argument)
	{
		$this->config = $argument;
	}


	/**
	 * Get a value from the config
	 *
	 * @param string $argument
	 * 
	 * @return <string, array>
	 */
	public function get($argument)
	{
		return $this->config[$argument];
	}
}