<?php 

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

require '/project/clever/vendor/autoload.php';

if (!verify_login()) die('{"succes":false}');

$req = $bdd->prepare("DELETE FROM persistent_login WHERE serial = :serial AND username = BINARY :username AND token = :token");
$req->execute(array(
	"serial" => $_COOKIE['serial'],
	"username" => $_COOKIE['username'],
	"token" => $_COOKIE['token']
));

setcookie("serial", '', 0, "/", "", true, true);
setcookie("username", '', 0, "/", "", true, true);
setcookie("token", '', 0, "/", "", true, true);

session_destroy();

echo '{"succes":true}';