<?php

namespace Clever\Library;

class Route
{
	private $routes = ['get' => [], 'post' => [], 'put' => [], 'delete' => []];

	/**
	 * Will be set in front of every add() unitl replaced or set to null
	 */
	public $basepath;


	/**
	 * Add uri to routes.
	 *
	 * @param string $id
	 * @param callable $function
	 * @param string $method
	 * 
	 * @return void
	 */
	public function add($uri, callable $function, $method = 'get')
	{

		if (!in_array(strtolower($method), ['get', 'post', 'put', 'delete'])) {
			
			trigger_error("Undefined method, " . $uri . " ignored.", E_USER_WARNING);
			return;
		}

		$this->routes[strtolower($method)][$this->basepath.$uri] = $function;
	}


	/**
	 * Run the router.
	 * 
	 * @return callback
	 */
	public function run()
	{

		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

		if (!array_key_exists($uri, $this->routes[$method])) {

			header("HTTP/1.0 404 Not Found");
			die();
		}

		return call_user_func_array($this->routes[$method][$uri], func_get_args());
	}
}