<?php 

use Clever\Library\Route;

use Clever\Library\Controller\PreGame\Create;

use Clever\Library\Controller\Login\Login;
use Clever\Library\Controller\Login\Username;
use Clever\Library\Controller\Login\Register;
use Clever\Library\Controller\Login\CookieLogin;
use Clever\Library\Controller\Login\Logout;
use Clever\Library\Controller\Login\UpdateCredentials;
use Clever\Library\Controller\Login\ResetPassword;


require dirname(__DIR__, 2) . '/vendor/autoload.php';


header('X-Robots-Tag: noindex');

if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {

	header("HTTP/1.0 400 Bad Request");
	die();
}

header('Content-Type: application/json; charset=utf-8');
//sleep(1);


$route = new Route();


$route->add("/call/game/socket_error", function() {

});


/**
 * Path from here is /call/pregame/(.*)
 */
$route->basepath = "/call/pregame";


/**
 * Create a game
 */
$route->add("/create" , [Create::class, 'create']);


/**
 * Path from here is /call/login/(.*)
 */
$route->basepath = "/call/login";


/**
 * Login
 */
$route->add("/login" , [Login::class, 'login'], 'post');


/**
 * Verify if username is already in use at registration
 */
$route->add("/username" , [Username::class, 'usernameExist']);


/**
 * Registration
 */
$route->add("/register" , [Register::class, 'register'], 'post');


/**
 * See if user is logged in via password or cookies
 */
$route->add("/cookie" , [CookieLogin::class, 'UserLoginWithCookie']);


/**
 * Logout
 */
$route->add("/logout" , [Logout::class, 'logout']);


/**
 * Verify the password if logged in with cookies.
 */
$route->add("/verifyPassword" , [CookieLogin::class, 'verifyPassword'], 'post');


/**
 * Update the email.
 */
$route->add("/updateEmail" , [UpdateCredentials::class, 'updateEmail'], 'post');


/**
 * Update the password.
 */
$route->add("/updatePassword" , [UpdateCredentials::class, 'updatePassword'], 'post');


/**
 * Password reset request.
 */
$route->add("/reset-password" , [ResetPassword::class, 'passwordMail'], 'post');


/**
 * Password reset request (after email).
 */
$route->add("/reset-password-link" , [ResetPassword::class, 'passwordReset'], 'post');



echo json_encode($route->run($database, $config));