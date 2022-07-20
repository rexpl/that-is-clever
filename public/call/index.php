<?php 

use Clever\Library\Route;
use Clever\Library\App\Login;
use Clever\Library\App\Register;

require dirname(__DIR__, 2) . '/vendor/autoload.php';



if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

	header("HTTP/1.0 404 Not Found");
	die();
}

header('Content-Type: application/json; charset=utf-8');


Route::add("/call/game/socket_error", function() {
	echo "hello";
});


/**
 * Path from here is /call/login/(.*)
 */
Route::$basepath = "/call/login";

/**
 * Login
 */
Route::add("/login" , function() {

	global $database;
	return Login::login($database);

}, 'post');

/**
 * Verify if username is already in use at registration
 */
Route::add("/username" , function() use ($database) {

	return Register::username($database, trim($_GET['username']));

});

/**
 * Registration
 */
Route::add("/register" , function() {

	global $database;
	return Register::register($database);

}, 'post');


echo json_encode(Route::run());