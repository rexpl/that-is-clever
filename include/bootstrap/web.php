<?php

use Clever\Library\App\Database;
use Clever\Library\App\Login;


$GLOBALS['database'] = new Database($config);

global $database;


session_start();


/**
 * Procedure to verify if the user needs to be login and then if he is logged in.
 */
$isGuestURL = in_array(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), require dirname(__DIR__, 2) . '/config/guest_url.php') ? true : false;

//url for guest but also visible for connected users
$isAllowedGuestURL = in_array(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), require dirname(__DIR__, 2) . '/config/guest_url_allowed.php') ? true : false;

$isLogin = Login::verifyLogin($database, $config);

$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? true : false;

if (!$isGuestURL) {

	if (!$isLogin) {
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

			header("HTTP/1.1 401 Unauthorized");
			die();
		}

		header('Location: '.$config->get('url').'/login');
		die();
	}
}
elseif ($isGuestURL && $isLogin && !$isAjax && !$isAllowedGuestURL) {

	header('Location: '.$config->get('url').'/home');
	die();
}



/**
 * Procedure to set the language
 */
function lang($value='') {
	
	static $lang;

	if (!empty($value)) $lang = $value;

	return $lang;
}

if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $config->get('supported_lang'))) {

	define('TEXT', json_decode(file_get_contents(dirname(__DIR__, 2) . '/lang/' . $_COOKIE['lang'] . '.json'), true));
	lang($_COOKIE['lang']);
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

	if (isset($_GET['language'],) && in_array($_GET['language'], $config->get('supported_lang'))) {

		$lang = $_GET['language'];
	}
	else {

		$lang = detect_language($config);
	}

	define('TEXT', json_decode(file_get_contents(dirname(__DIR__, 2) . '/lang/' . $lang . '.json'), true));
	setcookie("lang", $lang, time()+31556926, "/");

	lang($lang);
}

/**
 * The user changed from exemple.com/en/exemple to exemple.com/fr/exemple
 */
if (isset($_GET['language'], $_COOKIE['lang']) && $_GET['language'] != $_COOKIE['lang']) {

	if (!in_array($_GET['language'], $config->get('supported_lang'))) {

		header("HTTP/1.0 404 Not Found");
		die;
	}

	setcookie("lang", $_GET['language'], time()+31556926, "/");

	header('Location: '.parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
	die;
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

	return 'translatation.error [' . $argument . ']';
}


if ($isGuestURL) {
	
	require 'guest.php';
}