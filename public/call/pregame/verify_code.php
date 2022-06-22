<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die('{"success":false}');

if (!verify_login() || userIsInGame()) die('{"success":false}');

if (!isset($_GET['q']) && strlen($_GET['q']) !== 8) die('{"success":false}');

$req = $bdd->prepare("SELECT id FROM game WHERE type = 3 AND status = 3 AND token = :token");
$req->execute([$_GET['q']]);
$result = $req->fetch();

if (!$result) {
	die('{"success":false}');
}
$_SESSION['friend_game_id'] = $result['id'];

echo '{"success":true,"id":'.$result['id'].'}';