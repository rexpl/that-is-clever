<?php 

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\KeyProtectedByPassword;

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

//we don't query on empty fields
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['password_confirm']) || empty($_POST['mail'])) die(json_encode(array(
	"succes" => false,
	"message" => TEXT['login_fill_fields']
)));


//username does not exist
$req = $bdd->prepare("SELECT username FROM user WHERE username = :username");
$req->execute(array("username" => $_POST['username']));
$result = $req->fetch();

if ($result) die(json_encode(array(
	"succes" => false,
	"message" => TEXT['register_username_match']
)));

//passwords match and are compliant
$regex_check = array(
	'@[A-Z]@',
	'@[a-z]@',
	'@[0-9]@',
	'@[^\w]@'
);
$response = array(
	'register_character_password_up',
	'register_character_password_low',
	'register_character_password_num',
	'register_character_password_spe'
);

foreach ($regex_check as $key => $value) {
	if (!preg_match($value, $_POST['password'])) {
		die(json_encode(array(
			"succes" => false,
			"message" => TEXT['register_character_password'] . " " . TEXT[$response[$key]]
		)));
	}
}

if (strlen($_POST['password']) < 8) {
	die(json_encode(array(
		"succes" => false,
		"message" => TEXT['register_character_password'] . " " . TEXT['register_character_password_count']
	)));
}

if ($_POST['password'] !== $_POST['password_confirm']) {
	die(json_encode(array(
		"succes" => false,
		"message" => TEXT['register_password_no_match']
	)));
}

//mail is a valid mail address
if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
	die(json_encode(array(
		"succes" => false,
		"message" => TEXT['register_no_valid_mail']
	)));
}

//encrypt mail and hash password

//create protected key
$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($_POST['password']);
$protected_key_encoded = $protected_key->saveToAsciiSafeString();

//unlock key to encrypt mail
$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
$user_key = $protected_key->unlockKey($_POST['password']);

//finaly encrypt
$mail = Crypto::encrypt($_POST['mail'], $user_key);

$mail_hash = password_hash($_POST['mail'], PASSWORD_BCRYPT, ['cost' => 12]);

$password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]);

$req = $bdd->prepare("INSERT INTO user (id, status, username, password, mail_hash, protected_key, mail, failed_login_count) VALUES (NULL, 0, :username, :password, :mail_hash, :protected_key, :mail, 0)");
$req->execute(array(
	"username" => $_POST['username'],
	"password" => $password,
	"mail_hash" => $mail_hash,
	"protected_key" => $protected_key_encoded,
	"mail" => $mail
));

setcookie("username", $_POST['username'], time()+5443200, "/");

echo '{"succes":true}';