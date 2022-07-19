<?php

use Clever\Library\App\Database;
use Clever\Library\App\Login;


$GLOBALS['database'] = new Database($config);

global $database;


/**
 * Procedure to verify if the user needs to be login and then if he is logged in.
 */
$guestURL = in_array(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), require '../config/guest_url.php') ? true : false;

if (!$guestURL) {

	if (!Login::verifyLogin($database, $config)) {
		
		if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

			header("HTTP/1.1 401 Unauthorized");
			die();
		}

		header('Location: '.$config->get('url').'/login');
		die();
	}
}
elseif ($guestURL && Login::verifyLogin($database, $config)) {

	header('Location: '.$config->get('url').'/home');
	die();
}


/**
 * Procedure to set the language
 */
if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $config->get('url'))) {

	define('TEXT', json_decode(file_get_contents('../lang/' . $_COOKIE['lang'] . '.json'), true));
}
else {

	/**
	 * Return the preferred language.
	 *
	 * @param Clever\Library\App\Config
	 * 
	 * @return string
	 */
	function detect_language($config) {

		foreach (preg_split('/[;,]/', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $sub) {

			if (substr($sub, 0, 2) == 'q=') continue;
			if (strpos($sub, '-') !== false) $sub = explode('-', $sub)[0];
			if (in_array(strtolower($sub), $config->get('supported_lang'))) return $sub;
		}

		return $config->get('default_lang');

	}

	$lang = detect_language($config);
	define('TEXT', json_decode(file_get_contents('../lang/' . $lang . '.json'), true));
	setcookie("lang", $lang, time()+31556926, "/");
}


/**
 * Returns the required  text or $argument if text not found
 *
 * @param string $argument
 * 
 * @return string
 */
function t($argument) {

	if (isset(TEXT[$argument])) return TEXT[$argument];

	return $argument;
}