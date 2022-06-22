<?php

require '/project/clever/vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

if (!userIsInGame()) {
	header('Location: /home');
	die();
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
	<link rel="icon" type="image/x-icon" href="/ressources/img/logo.png">
	<link rel="stylesheet" type="text/css" href="/ressources/css/game.min.css?v=<?= CLEVER_VERSION; ?>">
</head>
<body class="bg-dark">
	<div class="modal" id="myModal_player" style="display: none;background-color: rgba(0,0,0,0.15);color: white;z-index: 1;"></div>
	<div class="modal" id="myModal_end" style="display: none;background-color: rgba(0,0,0,0.75);color: white;z-index: 2;" onclick="window.location.replace('/result/friend');">
		<div class="d-flex" style="height: 100%;">
			<div class="my-auto mx-auto">
				<div class="animate__animated animate__zoomIn" style="text-align: center;">
					<div class="m-3">
						<h1><?= TEXT['game_completed']; ?></h1>
						<h4 id="text_score"></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="myModal_round" style="display: none;background-color: rgba(0,0,0,0.75);color: white;z-index: 2;" onclick="document.getElementById('myModal_round').style.display = 'none';document.getElementById('myModal').style.opacity = 1;">
		<div class="d-flex" style="height: 100%;">
			<div class="my-auto mx-auto">
				<div class="animate__animated animate__zoomIn" style="text-align: center;">
					<div class="m-3">
						<h1 id="text_round"></h1>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.75);color: white;z-index: 1;" onclick="document.getElementById('myModal').style.display = 'none';">
		<div class="d-flex" style="height: 100%;">
			<div class="my-auto mx-auto">
				<div class="animate__animated animate__zoomIn" style="text-align: center;">
					<div class="m-3">
						<h1><?= TEXT['game_new_bonus']; ?></h1>
					</div>
					<div id="modal_img"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal" id="undermySidebar">
		<div id="mySidebar" class="sidebar animate__animated animate__fadeInLeft"  onclick="return false;">
			<div style="position: relative;">
				<div class="fixed-top">
					<h1><?= TEXT['game_chat']; ?></h1><hr>
				</div>
				<div class="fixed-main">
					<div class="all-message" id="all-message"></div>
				</div>
				<form class="fixed-bottom" autocomplete="off" onsubmit="return sendMessage();" id="form-message-this">
					<div class="clearfix w-100 border rounded p-1">
						<div class="input-group">
							<input class="form-control" type="text" name="message" id="message" placeholder="<?= TEXT['game_message']; ?>">
							<button class="btn btn-link" onclick="sendMessage();"><img src="/ressources/img/send.svg"></button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div style="height: 100%; width: 100%;margin-left: 20%;" onclick="closeNav();"></div>
	</div>
	<div class="ping">
		Ping: <span id="ping"></span>
	</div>
	<div class="container-xl mt-3 text-white" id="main_container"></div>
	<div class="bottom_footer">
		<div class="row p-2">
			<div class="col">
				<button type="button" class="btn btn-link animate__animated animate__tada" onclick="openNav();" id="img-message"><img src="/ressources/img/chat.svg"></button>
			</div>
			<div class="col text-white">
				<div class="center">
					<h2 id="text_player"><?= TEXT['game_waiting_connect']; ?></h2>
				</div>
			</div>
			<div class="col">
				<div class="float-end">
					<button type="button" class="btn btn-link" onclick="window.location.replace('/home');"><img src="/ressources/img/log-out.svg"></button>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript" textRound="<?= TEXT['game_round_js']; ?>" textPoints="<?= TEXT['game_points_js']; ?>" textConnection="<?= TEXT['game_no_connection']; ?>" textPlayer="<?= TEXT['game_player_js']; ?>" textLeft="<?= TEXT['game_user_left_js']; ?>" src="/ressources/js/friend.min.js?v=<?= CLEVER_VERSION; ?>" id="script"></script>
</body>
</html>