<?php 

use Clever\Library\Route;

use Clever\Library\App\Login;
use Clever\Library\App\Register;
use Clever\Library\App\Credentials;

require dirname(__DIR__, 2) . '/vendor/autoload.php';


header('X-Robots-Tag: noindex');
sleep(1);

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

	header("HTTP/1.0 400 Bad Request");
	die();
}

header('Content-Type: application/json; charset=utf-8');


Route::add("/call/game/socket_error", function() {
	return "hello";
});


/**
 * Path from here is /call/login/(.*)
 */
Route::$basepath = "/call/login";


/**
 * Login
 */
Route::add("/login" , function($database, $config)
{

	return Login::login($database, $config);

}, 'post');


/**
 * Verify if username is already in use at registration
 */
Route::add("/username" , function($database)
{

	return Register::username($database, trim($_GET['username']));

});


/**
 * Registration
 */
Route::add("/register" , function($database, $config)
{

	return Register::register($database, $config);

}, 'post');


/**
 * See if user is logged in via password or cookies
 */
Route::add("/cookie" , function()
{

	if ($_SESSION['cookie_login']) return true;
	
	return false;
});


/**
 * Logout
 */
Route::add("/logout" , function($database, $config)
{

	return Login::logout($database, $config, $_GET['all']);

}, 'get');


/**
 * Verify the password if logged in with cookies.
 */
Route::add("/verifyPassword" , function($database, $config)
{

	return Login::verifyPassword($database, $config);

}, 'post');


/**
 * Update the email.
 */
Route::add("/updateEmail" , function($database, $config)
{

	return Credentials::updateEmail($database, $config);

}, 'post');


/**
 * Update the password.
 */
Route::add("/updatePassword" , function($database, $config)
{

	return Credentials::updatePassword($database, $config);

}, 'post');


/**
 * Password reset request.
 */
Route::add("/reset-password" , function($database, $config)
{

	return Credentials::passwordMail($database, $config);

}, 'post');


/**
 * Password reset request.
 */
Route::add("/reset-password-link" , function($database, $config)
{

	return Credentials::resetPassword($database, $config);

}, 'post');



echo json_encode(Route::run($database, $config));