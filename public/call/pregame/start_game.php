<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die('{"success":false}');

if (!verify_login() || !userIsInGame()) die('{"success":false}');

if ($bdd->query("SELECT count(1) FROM game_players WHERE id_game = " . $_SESSION['game_id'])->fetchColumn() == 1) die('{"success":false}');

$req = $bdd->prepare("UPDATE game SET status = 2 WHERE id = :id_game");
$req->execute(array($_SESSION['game_id']));