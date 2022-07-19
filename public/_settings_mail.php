<?php 

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

require '../vendor/autoload.php';

if (!verify_login()) {
	header('Location: /login');
	die();
}

userIsInGame(true);

if ($_SESSION['clever_cookie_login']) {
	header('Location: /settings');
	die();
}

$req = $bdd->prepare("SELECT mail FROM user WHERE id = :id_user");
$req->execute(array("id_user" => $_SESSION['clever_user_id']));
$user_mail = $req->fetch()['mail'];

$key = Key::loadFromAsciiSafeString($_SESSION['user_key_encoded']);

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
				<h4><?= TEXT['settings_account_mail']; ?>:</h4><hr style="height:2.5px;" />
				<div class="py-2 px-3">
					<div class="my-2">
						<div class="alert alert-dismissible" id="alert_box" style="display: none;">
							<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
							<div id="text_alert">HEy</div>
						</div>
					</div>
					<p id="main_text"><?php echo sprintf(TEXT['settings_mail_message'], htmlentities(Crypto::decrypt($user_mail, $key), ENT_QUOTES)); ?></p>
					<form method="post" class="mt-4" autocomplete="on" onsubmit="return submit_form();" id="new_mail_form">
						<div class="mb-3">
							<label for="mail" class="form-label"><strong><?= TEXT['settings_mail_new']; ?>:</strong></label>
							<input type="text" class="form-control form-width" name="email" id="mail" onfocus="show()" onfocusout="hide()" onkeyup="check_email(this.value);" autocomplete="email">
							<div id="message_mail" class="mt-1" style="display: none;"></div>
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= TEXT['settings_mail_password']; ?>:</strong></label>
							<input type="password" class="form-control form-width" name="password" id="password" autocomplete="current-password">
						</div>
					</form>
				</div>
				<hr/>
				<div class="float-sm-end">
					<button class="btn btn-secondary" type="submit" form="new_mail_form"><?= TEXT['settings_mail_save']; ?></button>
					<button class="btn btn-outline-secondary" onclick="document.getElementById('new_mail_form').reset()"><?= TEXT['settings_mail_cancel']; ?></button>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	function hide() {
		var e = document.getElementById('message_mail');
		e.style.display = 'none';
	}

	function show() {
		var e = document.getElementById('message_mail');
		e.style.display = 'block';
	}

	function check_email(value) {
		var div = document.getElementById("message_mail");

		if (value.length > 0) {

			div.classList.add("text-danger");

			if (value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
				div.classList.remove("text-danger");
				div.classList.add("text-success");
				div.innerHTML = "<?= TEXT['register_valid_mail']; ?>";
				return true;
			}
			else {
				div.innerHTML = "<?= TEXT['register_no_valid_mail']; ?>";
				return false;
			}
		}
		else {
			return false;
		}
	}

	function submit_form() {
		document.getElementById("password").classList.remove("invalid");
		var e = document.getElementById("alert_box");
		e.style.display='none';
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4) {
				if (this.status == 200) {
					result = JSON.parse(this.responseText);
					if (result['succes'] == true) {
						document.getElementById('mail').value = '';
						e.classList.add("alert-success");
						document.getElementById("text_alert").innerHTML = result['message'];
						e.style.display='block';
						document.getElementById("main_text").innerHTML = result['new_text'];
					}
					else {
						if (result['reason'] == 'credentials') {
							document.getElementById("password").classList.add("invalid");
							document.getElementById('password').focus();
						}
						e.classList.add("alert-warning");
						document.getElementById("text_alert").innerHTML = result['message'];
						e.style.display='block';
					}
				}
				else {
					e.classList.add("alert-danger");
					document.getElementById("text_alert").innerHTML = "<?= TEXT['unexpected_error']; ?>";
					e.style.display='block';
				}
			}
		}
		var data = new FormData(document.getElementById('new_mail_form'));
		document.getElementById('password').value = '';
		xhttp.open("POST", "/call/login/update_mail.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(data);

		return false;
	}
</script>
</body>
</html>