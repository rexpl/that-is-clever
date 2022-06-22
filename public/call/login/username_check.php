<?php 

require '/project/clever/vendor/autoload.php';

//request ajax
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') die(json_encode(array("succes" => false)));

//we don't query on empty fields
if (empty($_GET['username'])) {
	header("HTTP/1.1 400 Bad Request");
	die();
}

$req = $bdd->prepare("SELECT 1 FROM user WHERE username = :username");
$req->execute(array("username" => $_GET['username']));
$result = $req->fetch();

if ($result) {
	die('{"succes":true}');
}
else {
	die('{"succes":false}');
}