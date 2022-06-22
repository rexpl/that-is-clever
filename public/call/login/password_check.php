<?php 

use Defuse\Crypto\KeyProtectedByPassword;

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

require '/project/clever/vendor/autoload.php';

if (!verify_login()) die('{"succes":false}');

//user has submitted the password
if (!$_SESSION['clever_cookie_login']) {
	die('{"succes":true,"check":true}');
}

//no password submitted, it is just a check
if (!isset($_POST['password']) || empty($_POST['password'])) {
	die('{"succes":true,"check":false}');
}

$req = $bdd->prepare("SELECT password, protected_key FROM user WHERE id = :id_user AND status = 0");
$req->execute(array("id_user" => $_SESSION['clever_user_id']));
$user_data = $req->fetch();

if (password_verify($_POST['password'], $user_data['password'])) {

	//we unlock the encryption key

	$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($user_data['protected_key']);
	$user_key = $protected_key->unlockKey($_POST['password']);
	$user_key_encoded = $user_key->saveToAsciiSafeString();

	$_SESSION['clever_cookie_login'] = false;
	$_SESSION['user_key_encoded'] = $user_key_encoded;
	die('{"succes":true,"check":true}');
}


echo '{"succes":true,"check":false}';
