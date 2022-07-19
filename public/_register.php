<?php 

require '../vendor/autoload.php';

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= t('register_register'); ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/x-icon" href="<?= $config->get('url'); ?>/ressources/img/logo.png">

	<link href="<?= $config->get('url'); ?>/ressources/css/login.min.css?v=<?= $config->get('version'); ?>" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

	<style type="text/css">
		body {
			font-family: "Lato", sans-serif;
		}
	</style>
</head>
<body>
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.3);">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="m-3">
					<button type="button" class="btn-close float-end" onclick="document.getElementById('myModal').style.display = 'none';"></button>
				</div>
				<div class="modal-body mx-auto my-1">
					<div class="mb-5" style="text-align: center;">
						<img src="/ressources/img/dices.png" height="100" width="auto">
					</div>
					<h3><?php echo TEXT['register_success']; ?></h3>
				</div>
				<div class="m-3 mx-auto">
					<a href="/login" class="btn btn-success"><?php echo TEXT['resgiter_succes_login']; ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="sidenav"><div class="login-main-text"><div id="wrapper_codepen"><div id="dice_codepen"><div class="side front"><div class="dot center"></div></div><div class="side front inner"></div><div class="side top"><div class="dot dtop dleft"></div><div class="dot dbottom dright"></div></div><div class="side top inner"></div><div class="side right"><div class="dot dtop dleft"></div><div class="dot center"></div><div class="dot dbottom dright"></div></div><div class="side right inner"></div><div class="side left"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side left inner"></div><div class="side bottom"><div class="dot center"></div><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side bottom inner"></div><div class="side back"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div><div class="dot center dleft"></div><div class="dot center dright"></div></div><div class="side back inner"></div><div class="side cover x"></div><div class="side cover y"></div><div class="side cover z"></div></div></div></div></div>
	<div class="main">
		<div class="col-md-6 col-sm-12">
			<div class="login-form">
				<form autocomplete="on" onsubmit="return register();" id="register_form">
					<div class="m-3">
						<div class="mobile_dot">
							<div class="alert alert-dismissible" id="alert_box" style="visibility: hidden;">
								<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.visibility='hidden';"></button>
								<div id="text_alert"></div>
							</div>
						</div>
						<div class="mb-3">
							<h1>Clever - <?= TEXT['register_register']; ?></h1>
						</div>
						<div id="fields">
							<div class="mb-3">
								<label for="username" class="form-label"><strong><?= TEXT['login_username']; ?>:</strong></label>
								<input type="text" autocomplete="username" class="form-control border" name="username" id="username" onfocus="show('message_username');check_username(this.value);" onfocusout="hide('message_username')" onkeyup="check_username(this.value);">
								<div id="message_username" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label"><strong><?= TEXT['login_password']; ?>:</strong></label>
								<input type="password" autocomplete="new-password" class="form-control border" name="password" id="password" onfocus="show('message_password');check_password(this.value);" onfocusout="hide('message_password')" onkeyup="check_password(this.value);">
								<div id="message_password" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-3">
								<label for="password_confirm" class="form-label"><strong><?= TEXT['register_confirm_password']; ?>:</strong></label>
								<input type="password" autocomplete="new-password" class="form-control border" name="password_confirm" id="password_confirm" onfocus="show('message_password_confirm');check_password_confirm(this.value);" onfocusout="hide('message_password_confirm')" onkeyup="check_password_confirm(this.value);">
								<div id="message_password_confirm" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-5">
								<label for="mail" autocomplete="email" class="form-label"><strong><?= TEXT['register_mail']; ?>:</strong></label>
								<input type="email" class="form-control border" name="mail" id="mail" onfocus="show('message_mail');check_email(this.value);" onfocusout="hide('message_mail')" onkeyup="check_email(this.value);">
								<div id="message_mail" class="mt-1" style="visibility: hidden;"></div>
							</div>
						</div>
						<button type="submit" class="btn btn-black text-white" id="butn_submit"><?= TEXT['register_register']; ?></button>
					</div>
				</form>
				<div class="m-3">
					<div class="mt-5" style="text-align: right;">
						<?php echo sprintf(TEXT['register_login'], "<a href=\"/login\" class=\"link\">", "</a>"); ?>
					</div>				
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	document.getElementById('username').focus();

	function register() {
		var e = document.getElementById("alert_box");
		e.style.visibility='hidden';
		const username = document.getElementById('username');
		const password = document.getElementById('password');
		const password_confirm = document.getElementById('password_confirm');
		const mail = document.getElementById('mail');
		
		if (check_username(username.value) && check_password(password.value) && check_password_confirm(password.value) && check_email(mail.value)) {
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					if (this.status == 200) {
						result = JSON.parse(this.responseText);
						if (result['succes'] == true) {
							username.value = '';mail.value = '';
							document.getElementById("myModal").style.display = "block";
						}
						else {
							e.classList.add("alert-warning");
							document.getElementById("text_alert").innerHTML = result['message'];
							e.style.visibility='visible';
						}
					}
					else {
						e.classList.add("alert-danger");
						document.getElementById("text_alert").innerHTML = "<?= TEXT['unexpected_error']; ?>";
						e.style.visibility='visible';
					}
				}
			}
			var data = new FormData(document.getElementById('register_form'));
			password.value = '';password_confirm.value = '';
			xhttp.open("POST", "/call/login/register.php", true);
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.send(data);
		}
		else {
			e.classList.add("alert-warning");
			document.getElementById("text_alert").innerHTML = "<?= TEXT['register_error_submit']; ?>";
			e.style.visibility='visible';
		}

		hide('message_username');
		hide('message_password');
		hide('message_password_confirm');
		hide('message_mail');

		return false;
	}

	function hide(argument) {
		var e = document.getElementById(argument);
		e.style.visibility = 'hidden';
		e.innerHTML = '';
	}

	function show(argument) {
		var e = document.getElementById(argument);
		e.style.visibility = 'visible';
	}

	function check_username(value) {
		if (value.length > 0) {
			var input = document.getElementById("username");
			var div = document.getElementById("message_username");
			if (value.length < 3) {
				div.classList.remove("text-success");
				div.classList.add("text-danger");
				input.classList.add("border-danger");
				div.innerHTML = "<?= TEXT['register_username_character']; ?>";
				return false;
			}

			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (this.readyState == 4) {
					if (this.status == 200) {
						result = JSON.parse(this.responseText);
						if (result['succes'] == true) {
							input.classList.add("border-danger");
							div.classList.remove("text-success");
							div.classList.add("text-danger");
							div.innerHTML = "<?= TEXT['register_username_match']; ?>";
							return false;
						}
					}
					else {
						var e = document.getElementById("alert_box");
						e.classList.add("alert-danger");
						document.getElementById("text_alert").innerHTML = "<?= TEXT['unexpected_error']; ?>";
						e.style.visibility='visible';
					}
				}
			}
			xhttp.open("GET", "/call/login/username_check.php?username=" + value, true);
			xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
			xhttp.send();

			input.classList.remove("border-danger");
			input.classList.add("border-success");
			div.classList.remove("text-danger");
			div.classList.add("text-success");
			div.innerHTML = "<?= TEXT['register_username_no_match']; ?>";
			return true;
		}
		else {
			return false;
		}
	}

	function check_password(value) {
		var input = document.getElementById("password");
		var div = document.getElementById("message_password");
		var count = 0;

		div.innerHTML = "<?= TEXT['register_character_password']; ?><br/><span id=\"password-low\">&emsp;- <?= TEXT['register_character_password_low']; ?></span><br/><span id=\"password-up\">&emsp;- <?= TEXT['register_character_password_up']; ?></span><br/><span id=\"password-num\">&emsp;- <?= TEXT['register_character_password_num']; ?></span><br/><span id=\"password-spe\">&emsp;- <?= TEXT['register_character_password_spe']; ?></span><br/><span id=\"password-count\">&emsp;- <?= TEXT['register_character_password_count']; ?></span>";

		if (value.length > 0) {
			input.classList.add("border-danger");
			div.classList.add("text-danger");

			//lower case
			if (value.match(/[a-z]/)) {
				document.getElementById("password-low").classList.add("text-success");
				count++;
			}

			//upper case
			if (value.match(/[A-Z]/)) {
				document.getElementById("password-up").classList.add("text-success");
				count++;
			}

			//number case
			if (value.match(/[0-9]/)) {
				document.getElementById("password-num").classList.add("text-success");
				count++;
			}

			//special case
			if (value.match(/[^a-zA-Z0-9 ]+/)) {
				document.getElementById("password-spe").classList.add("text-success");
				count++;
			}

			//count 8
			if (value.length >= 8) {
				document.getElementById("password-count").classList.add("text-success");
				count++;
			}

			if (count == 5) {
				input.classList.remove("border-danger");
				div.classList.remove("text-danger");
				input.classList.add("border-success");
				div.classList.add("text-success");
				div.innerHTML = "<?= TEXT['register_valid_password']; ?>";
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	function check_password_confirm(value) {
		var input = document.getElementById("password_confirm");
		var div = document.getElementById("message_password_confirm");

		if (value.length > 0) {

			input.classList.add("border-danger");
			div.classList.add("text-danger");

			if (document.getElementById("password").value === value) {
				input.classList.remove("border-danger");
				div.classList.remove("text-danger");
				input.classList.add("border-success");
				div.classList.add("text-success");
				div.innerHTML = "<?= TEXT['register_valid_password_confirm']; ?>";
				return true;
			}
			else {
				div.innerHTML = "<?= TEXT['register_password_no_match']; ?>";
				return false;
			}
		}
		else {
			return false;
		}
	}

	function check_email(value) {
		var input = document.getElementById("mail");
		var div = document.getElementById("message_mail");

		if (value.length > 0) {

			input.classList.add("border-danger");
			div.classList.add("text-danger");

			if (value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
				input.classList.remove("border-danger");
				div.classList.remove("text-danger");
				input.classList.add("border-success");
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
</script>
</body>
</html>