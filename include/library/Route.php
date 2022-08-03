<?php

namespace Clever\Library;

class Route
{
	private static $routes = ['get' => [], 'post' => [], 'put' => [], 'delete' => []];

	/**
	 * We be set in front of every add() unitl replaced or set to null
	 */
	public static $basepath;


	/**
	 * Add uri to routes.
	 *
	 * @param string $id
	 * @param function $function
	 * @param string $method
	 * 
	 * @return void
	 */
	public static function add($uri, $function, $method = 'get')
	{

		if (!in_array(strtolower($method), ['get', 'post', 'put', 'delete'])) {
			
			trigger_error("Undefined method, " . $uri . " ignored.", E_USER_WARNING);
			return;
		}

		self::$routes[strtolower($method)][self::$basepath.$uri] = $function;
	}


	/**
	 * Run the router.
	 * 
	 * @return callback
	 */
	public static function run()
	{

		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

		if (!array_key_exists($uri, self::$routes[$method])) {

			header("HTTP/1.0 404 Not Found");
			die();
		}

		return call_user_func_array(self::$routes[$method][$uri], func_get_args());
	}
}