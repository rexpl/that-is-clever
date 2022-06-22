<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die('{"success":false}');

if (!verify_login() || userIsInGame()) die('{"success":false}');

if (!isset($_GET['q']) && !in_array($_GET['q'], ['solo', 'friend'])) die('{"success":false}');

if ($_GET['q'] === "solo") {
	$type = 2;
	$token = '';
	$status = 2;
}
else {
	$type = 3;
	$status = 3;
	while (true) {
		$token = strtoupper(generate_token(8));
		$req = $bdd->prepare("SELECT id FROM game WHERE type = 3 AND token = :token");
		$req->execute(array(
			"token" => $token
		));
		$result = $req->fetch();
		if (!$result) break;
	}
}

$datetime_start = date("Y-m-d H:i:s");

$req = $bdd->prepare("INSERT INTO game (id, type, token, datetime_start, status) VALUES (NULL, :type, :token, :datetime_start, :status)");
$req->execute(array(
	"type" => $type,
	"token" => $token,
	"datetime_start" => $datetime_start,
	"status" => $status
));

//not sure..
$id_game = $bdd->lastInsertId();

$req = $bdd->prepare("INSERT INTO game_players (id, id_user, id_game, ip_on_submit, status, score, data) VALUES (NULL, :id_user, :id_game, :ip_on_submit, 0, 0, '')");
$req->execute(array(
	"id_user" => $_SESSION['clever_user_id'],
	"id_game" => $id_game,
	"ip_on_submit" => $_SERVER['REMOTE_ADDR']
));

$_SESSION['game_id'] = $id_game;
$_SESSION['game_type'] = $_GET['q'];

if ($_GET['q'] === 'friend') {
	echo '{"success":true,"token":"'.$token.'"}';
}
else {
	echo '{"success":true}';
}