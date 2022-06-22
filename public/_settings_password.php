<?php 

require '/project/clever/vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

userIsInGame(true);

if ($_SESSION['clever_cookie_login']) {
	header('Location: /settings');
	die();
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= TEXT['home_settings']; ?></title>
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
			<h1><?= TEXT['home_settings']; ?></h1>
		</div>
		<div class="clearfix mt-4">
			<span class="float-start"><button class="btn btn-secondary" onclick="window.location.href = '/settings'"><img src="/ressources/img/arrow-left.svg"></button></span>
		</div>
		<div class="mt-5">
			<div class="mb-4 mx-3">
				<h4><?= TEXT['settings_account_password']; ?>:</h4><hr style="height:2.5px;" />
				<div class="py-2 px-3">
					<div class="my-2">
						<div class="alert alert-dismissible" id="alert_box" style="display: none;">
							<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
							<div id="text_alert">HEy</div>
						</div>
					</div>
					<p id="main_text"><?= TEXT['settings_change_password_message']; ?></p>
					<form method="post" class="mt-4" autocomplete="on" onsubmit="return false;" id="new_mail_form">
						<div class="mb-3">
							<label for="mail" class="form-label"><strong><?= TEXT['settings_mail_password']; ?>:</strong></label>
							<input type="password" class="form-control form-width" name="password" id="password" autocomplete="current-password">
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= TEXT['settings_change_password_new']; ?>:</strong></label>
							<input type="password" class="form-control form-width" name="new_password" id="new_password" autocomplete="new-password">
							<div id="message_password" class="mt-1" style="display: none;"></div>
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= TEXT['settings_change_password_confirm']; ?>:</strong></label>
							<input type="password" class="form-control form-width" name="confirm_password" id="confirm_password" autocomplete="new-password">
							<div id="message_confirm_password" class="mt-1" style="visibility: hidden;">The two passwords don't match</div>
						</div>
					</form>
				</div>
				<hr/>
				<div class="float-sm-end">
					<button class="btn btn-secondary"><?= TEXT['settings_mail_save']; ?></button>
					<button class="btn btn-outline-secondary"><?= TEXT['settings_mail_cancel']; ?></button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>