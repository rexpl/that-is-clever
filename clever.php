<?php

use Workerman\Worker;

use Clever\Library\Game\Handler;

require 'vendor/autoload.php';


//variables needed for gameplay
$GLOBALS['values'] = array();
$GLOBALS['game'] = array();


// Create a Websocket server
$websocket = new Worker('websocket://0.0.0.0:8000');

$handler = new Handler($config, $websocket);

$websocket->onWorkerStart = [$handler, 'onWorkerStart'];

//function excecuted when new connection come
//onWebSocketConnect
$websocket->onWebSocketConnect = [$handler, 'onWebSocketConnect'];

/*//function excecuted when data received
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
};*/

//start service
Worker::runAll();