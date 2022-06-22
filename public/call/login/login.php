<?php 

use Defuse\Crypto\KeyProtectedByPassword;

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

//we don't query on empty fields
if (empty($_POST['username']) || empty($_POST['password'])) die(json_encode(array(
		"succes" => false,
		"reason" => "fields",
		"message" => TEXT['login_fill_fields']
	)));

$req = $bdd->prepare("SELECT id, password, protected_key, failed_login_count FROM user WHERE username = BINARY :username AND status = 0");
$req->execute(array("username" => $_POST['username']));
$user_data = $req->fetch();

//user does not exist
if (!$user_data) die(json_encode(array(
	"succes" => false,
	"reason" => "credentials",
	"message" => TEXT['login_failed_attempt']
)));

if ($user_data['failed_login_count'] < 8) {
	if (password_verify($_POST['password'], $user_data['password'])) {

		//we reset failed_login_count if needed
		if ($user_data['failed_login_count'] != 0) {
			$req = $bdd->prepare("UPDATE user SET failed_login_count = 0 WHERE id = :id_user");
			$req->execute(array("id_user" => $user_data['id']));
		}

		//we unlock the encryption key

		$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($user_data['protected_key']);
		$user_key = $protected_key->unlockKey($_POST['password']);
		$user_key_encoded = $user_key->saveToAsciiSafeString();


		//we create cookies/session for login and log in db, then exit

		$serial = generate_token(16);
		setcookie("serial", $serial, time()+5443200, "/", "", true, true);

		$token = generate_token(24);
		setcookie("token", $token, time()+1814400, "/", "", true, true);

		setcookie("username", $_POST['username'], time()+5443200, "/", "", true, true);

		$req = $bdd->prepare("INSERT INTO persistent_login (id, id_user, username, serial, token) VALUES (NULL, :id_user, :username, :serial, :token)");
		$req->execute(array(
			"id_user" => $user_data['id'],
			"username" => $_POST['username'],
			"serial" => $serial,
			"token" => $token
		));

		session_start();
		$_SESSION['clever_user_id'] = $user_data['id'];
		$_SESSION['clever_user_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['clever_cookie_login'] = false;
		$_SESSION['user_key_encoded'] = $user_key_encoded;
		die(json_encode(array("succes" => true)));
	}
	else {

		//password does not match we start security procedure

		$user_data['failed_login_count']++;

		$req = $bdd->prepare("UPDATE user SET failed_login_count = :count WHERE id = :id_user");
		$req->execute(array(
			"count" => $user_data['failed_login_count'],
			"id_user" => $user_data['id']
		));

		$req = $bdd->prepare("INSERT INTO login_attempt (id, id_user, date_time, failed_login_count) VALUES (NULL, :id_user, :date_time, :count)");
		$req->execute(array(
			"id_user" => $user_data['id'],
			"date_time" => date("Y-m-d H:i:s"),
			"count" => $user_data['failed_login_count']
		));

		//we show the blocked message if it is the 8th time that the wrong password has been entered
		$tmp = ($user_data['failed_login_count'] == 8) ? sprintf(TEXT['login_failed_attempt_blocked'], 15) : TEXT['login_failed_attempt'];
		die(json_encode(array(
			"succes" => false,
			"reason" => "credentials",
			"message" => $tmp
		)));
	}
}

//we are passed 8th atttempt
//we see since when the user is waiting
$req = $bdd->prepare("SELECT date_time FROM login_attempt WHERE id_user = :id_user ORDER BY date_time DESC LIMIT 1");
$req->execute(array(
	"id_user" => $user_data['id']
));
$waiting_time = time() - strtotime($req->fetch()['date_time']);

//see if user waited 15 minutes
if ($waiting_time < 900) {
	$waiting_time = 900 - $waiting_time;
	die(json_encode(array(
		"succes" => false,
		"reason" => "block_time",
		"message" => sprintf(TEXT['login_failed_attempt_blocked'], ceil($waiting_time / 60))
	)));
}

//the user waited 15 min
if (password_verify($_POST['password'], $user_data['password'])) {

	//we reset failed_login_count if needed
	if ($user_data['failed_login_count'] != 0) {
		$req = $bdd->prepare("UPDATE user SET failed_login_count = 0 WHERE id = :id_user");
		$req->execute(array("id_user" => $user_data['id']));
	}

	//we unlock the encryption key

	$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($user_data['protected_key']);
	$user_key = $protected_key->unlockKey($_POST['password']);
	$user_key_encoded = $user_key->saveToAsciiSafeString();


	//we create cookies/session for login and log in db, then exit

	$serial = generate_token(16);
	setcookie("serial", $serial, time()+5443200, "/", "", true, true);

	$token = generate_token(24);
	setcookie("token", $token, time()+1814400, "/", "", true, true);

	setcookie("username", $_POST['username'], time()+5443200, "/", "", true, true);

	$req = $bdd->prepare("INSERT INTO persistent_login (id, id_user, username, serial, token) VALUES (NULL, :id_user, :username, :serial, :token)");
	$req->execute(array(
		"id_user" => $user_data['id'],
		"username" => $_POST['username'],
		"serial" => $serial,
		"token" => $token
	));

	session_start();
	$_SESSION['clever_user_id'] = $user_data['id'];
	$_SESSION['clever_user_ip'] = $_SERVER['REMOTE_ADDR'];
	$_SESSION['clever_cookie_login'] = false;
	$_SESSION['user_key_encoded'] = $user_key_encoded;
	die(json_encode(array("succes" => true)));
}

$user_data['failed_login_count']++;

//if the password is still no good the user has to wait 15 min
$req = $bdd->prepare("INSERT INTO login_attempt (id, id_user, date_time, failed_login_count) VALUES (NULL, :id_user, :date_time, :count)");
$req->execute(array(
	"id_user" => $user_data['id'],
	"date_time" => date("Y-m-d H:i:s"),
	"count" => $user_data['failed_login_count']
));

die(json_encode(array(
	"succes" => false,
	"reason" => "credentials",
	"message" => sprintf(TEXT['login_failed_attempt_blocked'], 15)
)));