<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die('{"success":false}');

if (!verify_login() || !userIsInGame()) die('{"success":false}');

if ($bdd->query("SELECT status FROM game WHERE id = " . $_SESSION['game_id'])->fetchColumn() != 3) die('"start"');

$req = $bdd->prepare("SELECT user.username FROM game_players INNER JOIN user on game_players.id_user = user.id WHERE id_game = :id_game");
$req->execute(array(
	"id_game" => $_SESSION['game_id']
));

echo json_encode($req->fetchAll());