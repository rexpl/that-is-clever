<?php 

require '/project/clever/vendor/autoload.php';

if (!verify_login() || !userIsInGame()) 

if (isset($_GET['q'])) {
	$file = fopen("/project/clever/log/websocket.log", "a");

	$line = date("Y-m-d H:i:s") . " [clientSideError]\t[" . $_SESSION['clever_user_id'] . " - " . $_SERVER['REMOTE_ADDR'] . "]\t" . $_GET['q'] . PHP_EOL;

	if (fwrite($file, $line)) {

		throw new Exception("Error while writing to file.")
	}
}