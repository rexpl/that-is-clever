<?php 

use Clever\Library\Route;
use Clever\Library\App\Login;
use Clever\Library\App\Register;

require dirname(__DIR__, 2) . '/vendor/autoload.php';



if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

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
Route::add("/login" , function() use ($database, $config) {

	return Login::login($database, $config);

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
Route::add("/register" , function() use ($database, $config) {

	return Register::register($database, $config);

}, 'post');


echo json_encode(Route::run());