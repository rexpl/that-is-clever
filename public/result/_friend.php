<?php 

require '/project/clever/vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

if (!userIsInGame() || $_SESSION['game_type'] != 'friend') {
	header('Location: /home');
	die();
}

$req = $bdd->prepare("SELECT game_players.id_user, game_players.score, game_players.data, user.username  FROM game_players INNER JOIN user ON game_players.id_user = user.id WHERE id_game = :id ORDER BY score DESC");
$req->execute(array($_SESSION['game_id']));
$allData = $req->fetchAll();

unset($_SESSION['game_id']);
unset($_SESSION['game_type']);

$i = 0;

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= TEXT['home_play_friend']; ?></title>
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
			<h1><?= TEXT['home_play_friend']; ?></h1>
		</div>
		<div class="home-margin-top result-friend">
			<div id="accordion">
			<?php foreach($allData as $value): $i++; ?>
				<div class="card border-0">
					<div class="card-header bg-secondary">
						<a class="<?php if ($value['id_user'] != $_SESSION['clever_user_id']) echo 'collapsed' ?> btn btn-secondary" data-bs-toggle="collapse" href="#collapse<?= $i ?>">
							<h5><?php echo $i . "# " . htmlentities($value['username'], ENT_QUOTES) . " (" . $value['score'] . ")"; ?></h5>
						</a>
					</div>
					<div id="collapse<?= $i ?>" class="collapse <?php if ($value['id_user'] == $_SESSION['clever_user_id']) echo 'show' ?>" data-bs-parent="#accordion">
						<div class="card-body bg-dark">
							<?php $points = json_decode($value['data'], true)['points']; ?>
							<div class="row my-5">
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
								<?php

								if ($value['id_user'] == $_SESSION['clever_user_id']) {
									echo "<h3>" . sprintf(TEXT['game_points'], $points['total']) . "</h3><i>\"" . TEXT['game_phrase_'.get_id_phrase($points['total'])] . "\"</i>";
								}
								else {
									echo "<h3>" . $value['username'] . " " . sprintf(TEXT['game_end_username_points'], $points['total']) . "</h3>";
								}

								?>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<div class="my-5">
			<div class="center">
				<button class="btn btn-outline-secondary px-3" onclick="window.location.replace('/home');"><h2><?= TEXT['game_to_home']; ?></h2></button>&ensp;
				<button class="btn btn-secondary px-3" onclick="window.location.replace('/pregame/friend');"><h2><?= TEXT['game_next']; ?></h2></button>
			</div>
		</div>
	</div>
</body>
</html> 