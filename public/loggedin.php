<?php 

require '../vendor/autoload.php';

userIsInGame(true);

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="icon" type="image/x-icon" href="/ressources/img/logo.png">
	<link href="/ressources/css/clever.min.css?v=<?= CLEVER_VERSION; ?>" rel="stylesheet">
</head>
<body class="bg-dark" style="color: white;">
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.5);">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="m-3">
					<button type="button" class="btn-close float-end" onclick="document.getElementById('myModal').style.display = 'none';"></button>
				</div>
				<div class="modal-body mx-auto my-1" style="color: grey!important;">
					<p class="my-5"><?= TEXT['message_multiplayer']; ?></p>
				</div>
				<div class="m-3 mx-auto">
					<button onclick="document.getElementById('myModal').style.display = 'none';" class="btn btn-secondary"><?= TEXT['game_to_home']; ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="container home-margin-top" id="page-home">
		<div class="mb-5" style="text-align: center;font-family: 'PermanentMarker';">
			<h1 class="home-display"><img src="/ressources/img/dices-color-l.png" class="home-dice">&ensp;Clever&ensp;<img src="/ressources/img/dices-color-r.png" class="home-dice"></h1>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="clever-box my-3 mx-2 home-box" onmouseover="hover_start('img_multi', 'users')" onmouseout="hover_end('img_multi', 'users')" onclick="document.getElementById('myModal').style.display = 'block';">
					<h2 class="margin-icons"><img id="img_multi" src="/ressources/img/users.svg" class="home-dice">&ensp;<?= TEXT['home_multiplayer']; ?></h2>
				</div>
			</div>
			<div class="col-md-6">
				<div class="clever-box my-3 mx-2 home-box" onmouseover="hover_start('img_solo', 'user')" onmouseout="hover_end('img_solo', 'user')" onclick="window.location.href = '/pregame/solo'">
					<h2 class="margin-icons"><img id="img_solo" src="/ressources/img/user.svg" class="home-dice">&ensp;<?= TEXT['home_solo']; ?></h2>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="clever-box my-3 mx-2 home-box" onmouseover="hover_start('img_frie', 'smile')" onmouseout="hover_end('img_frie', 'smile')" onclick="window.location.href = '/pregame/friend'">
					<h2 class="margin-icons"><img id="img_frie" src="/ressources/img/smile.svg" class="home-dice">&ensp;<?= TEXT['home_play_friend']; ?></h2>
				</div>
			</div>
			<div class="col-md-6">
				<div class="clever-box my-3 mx-2 home-box" onmouseover="hover_start('img_sett', 'settings')" onmouseout="hover_end('img_sett', 'settings')" onclick="window.location.href = '/settings'">
					<h2 class="margin-icons"><img id="img_sett" src="/ressources/img/settings.svg" class="home-dice">&ensp;<?= TEXT['home_settings']; ?></h2>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function hover_start(x, q) {
			document.getElementById(x).src = "/ressources/img/" + q +"-red.svg";
		}

		function hover_end(x, q) {
			document.getElementById(x).src = "/ressources/img/" + q +".svg";
		}
	</script>
</body>
</html>