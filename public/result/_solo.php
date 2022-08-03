<?php 

require '/project/clever/vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

if (!userIsInGame() || $_SESSION['game_type'] != 'solo') {
	header('Location: /home');
	die();
}

$req = $bdd->prepare("SELECT data FROM game_players WHERE id_game = :id");
$req->execute(array($_SESSION['game_id']));

$game_data = json_decode($req->fetch()['data'], true);

$points = $game_data['points'];

$id_phrase = get_id_phrase($points['total']);

unset($_SESSION['game_id']);
unset($_SESSION['game_type']);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= TEXT['home_solo']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="icon" type="image/x-icon" href="/ressources/favicon.ico">
	<link href="/ressources/css/clever.min.css?v=<?= CLEVER_VERSION; ?>" rel="stylesheet">
</head>
<body class="bg-dark" style="color: white;">
	<div class="container home-margin-top">
		<div class="mb-5" style="text-align: center;font-family: 'PermanentMarker';">
			<h1 class="home-display" onclick="window.location.href = '/home'"><img src="/ressources/img/dices-color-l.png" class="home-dice">&ensp;Clever&ensp;<img src="/ressources/img/dices-color-r.png" class="home-dice"></h1>
		</div>
		<div class="home-margin-top" style="text-align: center;">
			<h1><?= TEXT['home_solo']; ?></h1>
		</div>
		<div class="row home-margin-top">
			<div class="col">
				<div id="b-2-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: #3284b8;">
					<h1 id="b-2-text"><?= $points['blue']; ?></h1>
				</div>
			</div>
			<div class="col">
				<div id="y-11-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: #dbc900;">
					<h1 id="y-11-text"><?= $points['yellow']; ?></h1>
				</div>
			</div>
			<div class="col">
				<div id="g-1-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: #6bb058;">
					<h1 id="g-1-text"><?= $points['green']; ?></h1>
				</div>
			</div>
			<div class="col">
				<div id="o-5-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: #dba100;">
					<h1 id="o-5-text"><?= $points['orange']; ?></h1>
				</div>
			</div>
			<div class="col">
				<div id="p-3-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: #a442f5;">
					<h1 id="p-3-text"><?= $points['purple']; ?></h1>
				</div>
			</div>
		</div>
		<div class="row mt-4">
			<div class="col-4"></div>
			<div class="col-2 center mx-auto">
				<img class="m-2" src="/ressources/game/bonus-3.png" width="40" height="auto">
				<div id="p-3-div" class="clever-box center m-3 p-2" style="background-color: #ffffff;color: red;">
					<h1 id="p-3-text"><?= $points['fox']; ?></h1>
				</div>
			</div>
			<div class="col-4"></div>
		</div>
		<div class="mt-4 center">
			<h3><?php echo sprintf(TEXT['game_points'], $points['total']); ?></h3>
			<i>"<?= TEXT['game_phrase_'.$id_phrase]; ?>"</i>
		</div>
		<div class="my-5">
			<div class="center">
				<button class="btn btn-outline-secondary px-3" onclick="window.location.replace('/home');"><h2><?= TEXT['game_to_home']; ?></h2></button>&ensp;
				<button class="btn btn-secondary px-3" onclick="window.location.replace('/pregame/solo');"><h2><?= TEXT['game_next']; ?></h2></button>
			</div>
		</div>
	</div>
</body>
</html> 