<?php 

require '/project/clever/vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

userIsInGame(true);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= TEXT['home_solo']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="icon" type="image/x-icon" href="/ressources/img/logo.png">
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
		<div class="clearfix mt-4">
			<span class="float-start"><button class="btn btn-secondary" onclick="window.location.href = '/home'"><img src="/ressources/img/arrow-left.svg"></button></span>
		</div>
		<div class="m-5 p-5" style="text-align: center;">
			<div id="img">
				<img src="/ressources/img/dices.png" height="100" width="auto">
			</div>
			<div class="mt-5" id="text">
				<button class="btn btn-secondary py-2 px-5" onclick="enter_solo_game();"><h3><?= TEXT['game_start_solo']; ?></h3></button>
			</div>
		</div>
	</div>
<script type="text/javascript">
	function enter_solo_game() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result = JSON.parse(this.responseText);
				if (result['success'] == true) window.location.href = '/game/solo';
			}
		}
		xhttp.open("GET", "/call/pregame/create_game.php?q=solo", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}
</script>
</body>
</html> 