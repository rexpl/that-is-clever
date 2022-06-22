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
	<meta charset="utf-8"><title>Clever - <?= TEXT['home_settings']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="icon" type="image/x-icon" href="/ressources/img/logo.png">
	<link href="/ressources/css/clever.min.css?v=<?= CLEVER_VERSION; ?>" rel="stylesheet">
</head>
<body class="bg-dark" style="color: white;">
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.3);color: grey;">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="m-3">
					<button type="button" class="btn-close float-end" onclick="document.getElementById('myModal').style.display = 'none';"></button>
				</div>
				<div class="modal-body mx-auto my-1">
					<div class="mb-5" style="text-align: center;">
						<img src="/ressources/img/key.svg" height="100" width="auto">
					</div>
					<h5><?= TEXT['settings_password_message']; ?></h5>
					<form class="mt-4" autocomplete="off" onsubmit="return verify_password();" id="verify_form">
						<input type="text" autocomplete="username" hidden value="<?php echo $_COOKIE['username']; ?>">
						<input type="password" class="form-control w-75 mx-auto" name="password" id="password" autocomplete="current-password" placeholder="<?= TEXT['settings_password_enter']; ?>">
					</form>
				</div>
				<div class="m-3 mx-auto">
					<button class="btn btn-secondary" type="submit" form="verify_form"><?= TEXT['settings_password_verify']; ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="container home-margin-top mb-5">
		<div class="mb-5" style="text-align: center;font-family: 'PermanentMarker';">
			<h1 class="home-display" onclick="window.location.href = '/home'"><img src="/ressources/img/dices-color-l.png" class="home-dice">&ensp;Clever&ensp;<img src="/ressources/img/dices-color-r.png" class="home-dice"></h1>
		</div>
		<div class="home-margin-top" style="text-align: center;">
			<h1><?= TEXT['home_settings']; ?></h1>
		</div>
		<div class="clearfix mt-4">
			<span class="float-start"><button class="btn btn-secondary" onclick="window.location.href = '/home'"><img src="/ressources/img/arrow-left.svg"></button></span>
		</div>
		<div class="mt-5">
			<div class="mb-4 mx-3">
				<h4><?= TEXT['settings_application']; ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3">
					<span class="float-start"><?= TEXT['settings_language']; ?>:</span>
					<span class="float-end">
						<select id="language" class="form-select" onchange="language(this.value);" style="cursor: pointer;">
							<?php 
								foreach (SUPPORTED_LANGUAGES as $value) {
									echo "<option value=" . $value . ">" . TEXT['name_for_' . $value] . "</option>";
								}
							?>
						</select>
					</span>
				</div>
			</div>
			<div class="mb-4 mx-3">
				<h4><?= TEXT['settings_account']; ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3 link-setting" onclick="click_it('email');">
					<span class="float-start"><?= TEXT['settings_account_mail']; ?></span>
					<span class="float-end"><img src="/ressources/img/arrow-right.svg"></span>
				</div><hr/>
				<div class="clearfix py-2 px-3 link-setting" onclick="click_it('password');">
					<span class="float-start"><?= TEXT['settings_account_password']; ?></span>
					<span class="float-end"><img src="/ressources/img/arrow-right.svg"></span>
				</div>
			</div>
			<div class="mb-4 mx-3">
				<h4><?= TEXT['settings_logout_this']; ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3 link-setting" onclick="logout()">
					<span class="float-start"><?= TEXT['settings_logout_this']; ?></span>
					<span class="float-end"><img src="/ressources/img/log-out.svg"></span>
				</div><hr/>
				<div class="clearfix py-2 px-3 link-setting" onclick="click_it('logout');">
					<span class="float-start"><?= TEXT['settings_logout_everywhere']; ?></span>
					<span class="float-end"><img src="/ressources/img/log-out.svg"></span>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	function get_cookie(name) {
		var match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
		if (match) return match[2];
	}

	document.getElementById('language').value = get_cookie('lang');

	function language(argument) {
		var CookieDate = new Date;
		CookieDate.setFullYear(CookieDate.getFullYear() +1);
		document.cookie = "lang=" + argument + "; expires=" + CookieDate.toGMTString() + "; path=/";
		setTimeout(function() {
			window.location.href = window.location.href;
		}, 350);
	}

	var requested_page = null;

	function click_it(argument) {
		requested_page = argument;
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4) {
				if (this.status == 200) {
					result = JSON.parse(this.responseText);
					if (result['check'] == true) {
						if (argument == 'logout') {
							logout(true);
						}
						else {
							window.location.href = "/settings/" + argument;
						}
					}
					else {
						document.getElementById("myModal").style.display = "block";
						document.getElementById('password').focus();
					}
				}
				else {
					e.classList.add("alert-danger");
					document.getElementById("text_alert").innerHTML = "<?= TEXT['unexpected_error']; ?>";
					e.style.visibility='visible';
				}
			}
		}
		xhttp.open("GET", "/call/login/password_check.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}

	function verify_password() {
		document.getElementById("password").classList.remove("invalid");
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4) {
				if (this.status == 200) {
					result = JSON.parse(this.responseText);
					if (result['check'] == true) {
						if (requested_page == 'logout') {
							logout(true);
						}
						else {
							window.location.href = "/settings/" + requested_page;
						}
					}
					else {
						document.getElementById("password").classList.add("invalid");
						document.getElementById('password').focus();
					}
				}
			}
		}
		var data = new FormData(document.getElementById('verify_form'));
		document.getElementById('password').value = '';
		xhttp.open("POST", "/call/login/password_check.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(data);

		return false;
	}

	function logout(all=false) {
		var link = (all) ? 'logout_all' : 'logout';
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) window.location.href = "/login";
		}
		xhttp.open("GET", "/call/login/" + link + ".php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}
</script>
</body>
</html>