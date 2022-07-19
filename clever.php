<?php

use Workerman\Worker;
use Clever\Library\Solo;
use Clever\Library\Friend;

require 'vendor/autoload.php';
$file = fopen("/project/clever/log/websocket.log", "a");

//variables needed for gameplay
$GLOBALS['values'] = array();
$GLOBALS['game'] = array();

$context = [
	'ssl' => [
		'local_cert' => __DIR__ . '/cert/cert.pem',
		'local_pk' => __DIR__ . '/cert/key.pem',
		'verify_peer' => false
	]
];

Worker::$logFile = '/project/clever/log/workerman.log';

// Create a Websocket server
$websocket = new Worker('websocket://0.0.0.0:8080', $context);
$websocket->transport = 'ssl';

$websocket->onError = function ($connection, $code, $msg) use ($file) {

	fwrite($file, date("Y-m-d H:i:s") . " Error on " . $connection->getRemoteIp() . " (" . $connection->id . ")" . PHP_EOL);
	fwrite($file, "Error code: " . $code . PHP_EOL);
	fwrite($file, $msg . PHP_EOL);
};

//function excecuted when new connection come
//onWebSocketConnect
$websocket->onWebSocketConnect = function ($connection) use ($websocket, $db, $file) {

	global $values;
	global $game;

	fwrite($file, date("Y-m-d H:i:s") . " New connection on " . $connection->getRemoteIp() . " (" . $connection->id . ")" . PHP_EOL);

	$bdd = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8', $db['username'], $db['password'], $db['options']);

	//get the id of the user

	$req = $bdd->prepare("SELECT id_user FROM persistent_login WHERE username = BINARY :username AND serial = BINARY :serial AND token = BINARY :token");
	$req->execute(array(
		"username" => $_COOKIE['username'], 
		"serial" => $_COOKIE['serial'], 
		"token" => $_COOKIE['token']
	));
	$user_data = $req->fetch();

	if (!$user_data) {
		$connection->destroy();
		return;
	}

	//verify user is in game and ip match then delete ip

	$req = $bdd->prepare("SELECT game_players.id_game, game_players.ip_on_submit, game.type FROM game_players INNER JOIN game ON game_players.id_game = game.id WHERE game_players.id_user = :id_user AND game.status = 2 ORDER BY datetime_start DESC LIMIT 1");
	$req->execute([$user_data['id_user']]);
	$game_data = $req->fetch();

	if (!$game_data || $game_data['ip_on_submit'] != $connection->getRemoteIp()) {
		$connection->destroy();
		return;
	}

	$req = $bdd->prepare("UPDATE game_players SET ip_on_submit = '', status = 4 WHERE id_user = :id_user AND id_game = :id_game");
	$req->execute(array(
		"id_user" => $user_data['id_user'],
		"id_game" => $game_data['id_game']
	));

	switch ($game_data['type']) {
		case 2: //solo

			$req = $bdd->prepare("UPDATE game SET status = 4 WHERE id = :id_game");
			$req->execute(array(
				"id_game" => $game_data['id_game']
			));

			$values[$connection->id] = array(
				'type' => 2,
				'id_game' => $game_data['id_game']
			);

			$game[$game_data['id_game']] = new Solo($game_data['id_game'], $connection->id, $user_data['id_user'], false);
			$response = $game[$game_data['id_game']]->response(null, $connection->id, true);

		break;
		case 3: //friend game

			$values[$connection->id] = array(
				'type' => 3,
				'id_game' => $game_data['id_game']
			);

			if (!isset($game[$game_data['id_game']])) {

				$req = $bdd->prepare("UPDATE game SET token = '' WHERE id = :id_game");
				$req->execute(array(
					"id_game" => $game_data['id_game']
				));

				$count_users = $bdd->query("SELECT count(1) FROM game_players WHERE id_game = " . $game_data['id_game'])->fetchColumn();

				$game[$game_data['id_game']] = new Friend($game_data['id_game'], $count_users);
			}

			$req = $bdd->prepare("SELECT username FROM user WHERE id = :id");
			$req->execute([$user_data['id_user']]);
			$username = $req->fetch()['username'];

			$game[$game_data['id_game']]->addUser($connection->id, $user_data['id_user'], $username);
			$response = $game[$game_data['id_game']]->response(null, $connection->id, true);

			if (is_array($response)) {
				$req = $bdd->prepare("UPDATE game SET status = 4 WHERE id = :id_game");
				$req->execute(array(
					"id_game" => $game_data['id_game']
				));
			}
			
		break;
		default: //multiplayer
			
		break;
	}

	if (!is_array($response)) return;

	foreach ($response as $value) {
		
		$result = $websocket->connections[$value['id_connection']]->send(json_encode($value['body']));

		if ($result === false) {

			$game[$id_game]->consoleLog('Failed to send data. Connection is closed by remote or send buffer is full.' ,__FILE__, __LINE__);
		}

	}
	
};

//function excecuted when data received
$websocket->onMessage = function ($connection, $data) use ($websocket) {

	$data = json_decode($data, true);

	if ($data === "latency") {

		$connection->send('"latency"');
		return;
	}

	global $values;
	global $game;

	if (!isset($values[$connection->id])) $connection->destroy();

	$id_game = $values[$connection->id]['id_game'];

	$response = $game[$id_game]->response($data, $connection->id);

	if (!is_array($response)) return;
	
	foreach ($response as $value) {
		
		$result = $websocket->connections[$value['id_connection']]->send(json_encode($value['body']));

		if ($result === false) {

			$game[$id_game]->consoleLog('Failed to send data. Connection is closed by remote or send buffer is full.' ,__FILE__, __LINE__);
		}

	}
};

//function excecuted when connection closed
$websocket->onClose = function ($connection) use ($websocket, $db, $file) {

	fwrite($file, date("Y-m-d H:i:s") . " Connection closed on " . $connection->getRemoteIp() . " (" . $connection->id . ")" . PHP_EOL);

	global $values;
	global $game;

	if (!isset($values[$connection->id])) return;

	$id_game = $values[$connection->id]['id_game'];

	$bdd = new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'].';charset=utf8', $db['username'], $db['password'], $db['options']);

	switch ($values[$connection->id]['type']) {
		case 2: //solo

			$result = $game[$id_game]->close();

			unset($values[$connection->id]);

			if ($result['points'] === 0) {

				$req = $bdd->prepare("DELETE FROM game WHERE id = :id_game");
				$req->execute(array(
					"id_game" => $id_game
				));

				$req = $bdd->prepare("DELETE FROM game_players WHERE id_game = :id_game");
				$req->execute(array(
					"id_game" => $id_game
				));

				return;
			}

			$game[$id_game]->saveGameLog("/project/clever/log/game/");
			unset($game[$id_game]);

			$JSON_data = json_encode(array(
				'points' => $result['points'],
				'board' => $result['board']
			));

			$req = $bdd->prepare("UPDATE game_players SET status = 1, data = :data, score = :score WHERE id_user = :id_user AND id_game = :id_game");
			$req->execute(array(
				"data" => $JSON_data,
				"score" => $result['points']['total'],
				"id_user" => $result['id_user'],
				"id_game" => $id_game
			));

			$req = $bdd->prepare("UPDATE game SET status = 1 WHERE id = :id_game");
			$req->execute(array(
				"id_game" => $id_game
			));

			return;

		break;
		case 3: //friend game
			
			$result = $game[$id_game]->close($connection->id);

			foreach ($result['users'] as $key => $value) {
					
				unset($values[$key]);
			}

			if ($result['points'] === 0) {

				$req = $bdd->prepare("DELETE FROM game WHERE id = :id_game");
				$req->execute(array(
					"id_game" => $id_game
				));

				$req = $bdd->prepare("DELETE FROM game_players WHERE id_game = :id_game");
				$req->execute(array(
					"id_game" => $id_game
				));

				foreach ($result['send'] as $value) {
		
					$websocket->connections[$value['id_connection']]->send(json_encode($value['body']));

				}

				return;
			}

			$game[$id_game]->saveGameLog("/project/clever/log/game/");
			unset($game[$id_game]);

			$req = $bdd->prepare("UPDATE game_players SET status = 1, data = :data, score = :score WHERE id_user = :id_user AND id_game = :id_game");

			foreach ($result['users'] as $key => $value) {

				$JSON_data = json_encode(array(
					'points' => $result['points'][$key],
					'board' => $result['board'][$key]
				));

				$req->execute(array(
					"data" => $JSON_data,
					"score" => $result['points'][$key]['total'],
					"id_user" => $value['id_user'],
					"id_game" => $id_game
				));
			}

			$req = $bdd->prepare("UPDATE game SET status = 1 WHERE id = :id_game");
			$req->execute(array(
				"id_game" => $id_game
			));

			return;

		break;
		default: //multiplayer
			
		break;
	}
};

//start service
Worker::runAll();