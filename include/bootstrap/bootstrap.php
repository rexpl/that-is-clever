<?php 

use Clever\Library\Config;

$GLOBALS['config'] = new Config(require dirname(__DIR__, 2) . '/config/config.php');

global $config;


/**
 * Loads all necessary function for the web application
 * Runs every check neccessary for the web application
 */
if (php_sapi_name() != 'cli') {

	require 'web.php';
}


/**
 * Simple escape, not in web.php because the websocket (runs from cli) needs it
 *
 * @param string $argument
 * 
 * @return string
 */
function e($argument) {

	return htmlentities($argument, ENT_QUOTES);
}