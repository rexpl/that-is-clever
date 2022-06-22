<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die('{"success":false}');

if (!verify_login() || userIsInGame()) die('{"success":false}');

if (!isset($_GET['q']) || !isset($_SESSION['friend_game_id']) || $_SESSION['friend_game_id'] != $_GET['q']) die('{"success":false}');

if ($bdd->query("SELECT count(1) FROM game_players WHERE id_game = " . $_SESSION['friend_game_id'])->fetchColumn() == 4) die('{"success":false}');

$req = $bdd->prepare("SELECT 1 FROM game_players WHERE id_game = :id_game AND id_user = :id_user");
$req->execute(array(
	"id_user" => $_SESSION['clever_user_id'],
	"id_game" => $_SESSION['game_id'],
));
$userAlreadyInGame = $req->fetch();

if ($userAlreadyInGame) die('{"success":false}');

$_SESSION['game_id'] = $_SESSION['friend_game_id'];
$_SESSION['game_type'] = 'friend';
unset($_SESSION['friend_game_id']);

$req = $bdd->prepare("INSERT INTO game_players (id, id_user, id_game, ip_on_submit, status, score, data) VALUES (NULL, :id_user, :id_game, :ip_on_submit, 0, 0, '')");
$req->execute(array(
	"id_user" => $_SESSION['clever_user_id'],
	"id_game" => $_SESSION['game_id'],
	"ip_on_submit" => $_SERVER['REMOTE_ADDR']
));

echo '{"success":true}';