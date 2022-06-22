<?php 

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

require '/project/clever/vendor/autoload.php';

if (!verify_login()) die('{"succes":false}');

if (empty($_POST['email']) || empty($_POST['password'])) die(json_encode(array(
	"succes" => false,
	"message" => TEXT['login_fill_fields']
)));

//user has submitted the password
if ($_SESSION['clever_cookie_login']) {
	die('{"succes":false');
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	die(json_encode(array(
		"succes" => false,
		"message" => TEXT['register_no_valid_mail']
	)));
}

$req = $bdd->prepare("SELECT password, mail FROM user WHERE id = :id_user AND status = 0");
$req->execute(array("id_user" => $_SESSION['clever_user_id']));
$user_data = $req->fetch();

if (!password_verify($_POST['password'], $user_data['password'])) {
	die('{"succes":false,"reason":"credentials","message":"' . TEXT['login_failed_attempt'] .'"}');
}

$key = Key::loadFromAsciiSafeString($_SESSION['user_key_encoded']);

if (Crypto::decrypt($user_data['mail'], $key) === $_POST['email']) {
	die('{"succes":false,"reason":"changes","message":"' . TEXT['settings_nochange'] .'"}');
}

$new_mail = Crypto::encrypt($_POST['email'], $key);

$new_mail_hash = password_hash($_POST['email'], PASSWORD_BCRYPT, ['cost' => 12]);

$req = $bdd->prepare("UPDATE user SET mail_hash = :mail_hash, mail = :mail WHERE id = :id");
$req->execute(array(
	"mail_hash" => $new_mail_hash,
	"mail" => $new_mail,
	"id" => $_SESSION['clever_user_id']
));

echo '{"succes":true,"new_text":"' . sprintf(TEXT['settings_mail_message'], htmlentities($_POST['email'], ENT_QUOTES)) . '","message":"' . TEXT['settings_mail_success'] . '"}';