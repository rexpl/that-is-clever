<?php 

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

require '/project/clever/vendor/autoload.php';

if (!verify_login() && $_SESSION['clever_cookie_login']) die('{"succes":false}');

$req = $bdd->prepare("DELETE FROM persistent_login WHERE id_user = :id_user");
$req->execute(array(
	"id_user" => $_SESSION['clever_user_id']
));

setcookie("serial", '', 0, "/", "", true, true);
setcookie("username", '', 0, "/", "", true, true);
setcookie("token", '', 0, "/", "", true, true);

session_destroy();

echo '{"succes":true}';