<?php 

const CLEVER_VERSION = '0.0';

$GLOBALS['db'] = array(
	'host' => 'localhost',
	'dbname' => 'clever',
	'username' => 'clever',
	'password' => 'fQ59FnWD#p35PB9s6QuVu2Fm',
	'options' => [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES => false,
	]
);

global $db;

$GLOBALS['bdd'] = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8', $db['username'], $db['password'], $db['options']);

define('SUPPORTED_LANGUAGES', ['en', 'fr', 'nl', 'it']);

if (http_response_code()) {
	if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], SUPPORTED_LANGUAGES)) {
		define('TEXT', json_decode(file_get_contents('/project/clever/lang/' . $_COOKIE['lang'] . '.json'), TRUE));
	}
	else {
		//https://stackoverflow.com/questions/3770513/detect-browser-language-in-php
		function detect_language($fallback='en') {
			foreach (preg_split('/[;,]/', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $sub) {
				if (substr($sub, 0, 2) == 'q=') continue;
				if (strpos($sub, '-') !== false) $sub = explode('-', $sub)[0];
				if (in_array(strtolower($sub), SUPPORTED_LANGUAGES)) return $sub;
			}
			return $fallback;
		}
		////

		$lang = detect_language();
		define('TEXT', json_decode(file_get_contents('/project/clever/lang/' . $lang . '.json'), TRUE));
		setcookie("lang", $lang, time()+31556926, "/");
	}
}

function generate_token($length) {
	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < $length; ++$i) {
		$pieces []= $keyspace[random_int(0, $max)];
	}
	return implode('', $pieces);
};

function verify_login() {

	session_start();
	
	//user is already logged in and ip is unchanged
	if (isset($_SESSION['clever_user_id']) && isset($_SESSION['clever_user_ip']) && is_numeric($_SESSION['clever_user_id']) && $_SESSION['clever_user_ip'] == $_SERVER['REMOTE_ADDR']) return true;

	//user is missing a cookie
	if (!isset($_COOKIE['token']) || !isset($_COOKIE['serial']) || !isset($_COOKIE['username'])) return false;

	global $bdd;

	//we first confront the serial and the username to the db
	$req = $bdd->prepare("SELECT id, id_user, token FROM persistent_login WHERE serial = BINARY :serial AND username = BINARY :username");
	$req->execute(array("serial" => $_COOKIE['serial'], "username" => $_COOKIE['username']));
	$data = $req->fetch();

	if (!$data) return false;

	if ($data['token'] == $_COOKIE['token']) {
		//we log the user in and generate a new token to prevent token reusability
		$new_token = generate_token(24);
		$req = $bdd->prepare("UPDATE persistent_login SET token = :token WHERE id = :id");
		$req->execute(array(
			"token" => $new_token,
			"id" => $data['id']
		));

		setcookie("token", $new_token, time()+1814400, "/", "", true, true);
		$_SESSION['clever_user_id'] = $data['id_user'];
		$_SESSION['clever_user_ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION['clever_cookie_login'] = true;
		return true;
	}

	//https://web.archive.org/web/20110924071300/http://jaspan.com/improved_persistent_login_cookie_best_practice
	//at this point the user has the cookies but they don't match anyhting in db so there might a potential security threat
	//we are gonna remove every login possibility for this user by deleting all possible cookies and removing his password from db
	//the user is gonna have to reset his password to use his account again

	setcookie("serial", $_COOKIE['serial'], time()-5443200, "/", "", true, true);
	setcookie("username", $_COOKIE['username'], time()-5443200, "/", "", true, true);
	setcookie("token", $_COOKIE['token'], time()-1814400, "/", "", true, true);
	
	$req = $bdd->prepare("DELETE FROM persistent_login WHERE id_user = :id_user");
	$req->execute(array("id_user" => $data['id_user']));

	$req = $bdd->prepare("UPDATE user SET status = 2 WHERE id = :id_user");
	$req->execute(array("id_user" => $data['id_user']));

	return false;
}

function userIsInGame($needAction = false) {

	if (!isset($_SESSION['game_id'])) return false;

	if (!$needAction) return true;

	global $bdd;

	$req = $bdd->prepare("DELETE FROM game WHERE id = :id_game AND NOT status = 1");
	$req->execute(array(
		"id_game" => $_SESSION['game_id']
	));

	$req = $bdd->prepare("DELETE FROM game_players WHERE id_game = :id_game AND NOT status = 1");
	$req->execute(array(
		"id_game" => $_SESSION['game_id']
	));

	unset($_SESSION['game_id']);
	unset($_SESSION['game_type']);
}

function get_id_phrase($points) {
	if ($points > 280) {
		$id_phrase = 1;
	}
	elseif ($points >= 260 && $points <= 280) {
		$id_phrase = 2;
	}
	elseif ($points >= 240 && $points <= 259) {
		$id_phrase = 3;
	}
	elseif ($points >= 220 && $points <= 239) {
		$id_phrase = 4;
	}
	elseif ($points >= 200 && $points <= 219) {
		$id_phrase = 5;
	}
	elseif ($points >= 180 && $points <= 199) {
		$id_phrase = 6;
	}
	elseif ($points >= 160 && $points <= 179) {
		$id_phrase = 7;
	}
	elseif ($points >= 140 && $points <= 159) {
		$id_phrase = 8;
	}
	else {
		$id_phrase = 9;
	}

	return $id_phrase;
}