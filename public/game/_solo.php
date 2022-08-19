<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
	<link rel="icon" type="image/x-icon" href="/ressources/favicon.ico">
	<link rel="stylesheet" type="text/css" href="/ressources/css/game.min.css?v=<?= $config->get('version') ?>">
</head>
<body class="bg-dark">
	<div class="modal" id="myModal_end" style="display: none;background-color: rgba(0,0,0,0.75);color: white;z-index: 2;" onclick="window.location.replace('/result/solo');">
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
					<div class="m-3">
						<button class="btn btn-secondary" onclick="document.getElementById('myModal').style.display = 'none';">OK</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="container-xl mt-3 text-white" id="main_container"></div>
	<div class="bottom_footer">
		<div class="clearfix p-2">
			<div class="float-end">
				<button type="button" class="btn btn-link" onclick="window.location.replace('/home');"><img src="/ressources/img/log-out.svg"></button>
			</div>
		</div>
	</div>
<script type="text/javascript" textRound="<?= t('game_round_js') ?>" textPoints="<?= t('game_points_js') ?>" textConnection="<?= t('game_no_connection') ?>" wsLink="<?= $config->get('ws_url') ?>?token=<?= $_SESSION['game_token'] ?? '' ?>" src="/ressources/js/solo.min.js?v=<?= $config->get('version') ?>" id="script"></script>
</body>
</html>