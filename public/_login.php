<?php

require '../vendor/autoload.php';

/*if (verify_login()) {
	header('Location: /home');
	die();
}*/

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= t('login_login'); ?></title>
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
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.5);">
		<div class="modal-dialog modal-lg modal-dialog-centered">
			<div class="modal-content">
				<div class="m-3">
					<button type="button" class="btn-close float-end" onclick="document.getElementById('myModal').style.display = 'none';"></button>
				</div>
				<div class="modal-body mx-auto my-1">
					<p class="my-5"><?= t('message_reset_password'); ?></p>
				</div>
				<div class="m-3 mx-auto">
					<button onclick="window.location.href = '<?= $config->get('url'); ?>/register';" class="btn btn-secondary"><?= t('register_register'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="sidenav"><div class="login-main-text"><div id="wrapper_codepen"><div id="dice_codepen"><div class="side front"><div class="dot center"></div></div><div class="side front inner"></div><div class="side top"><div class="dot dtop dleft"></div><div class="dot dbottom dright"></div></div><div class="side top inner"></div><div class="side right"><div class="dot dtop dleft"></div><div class="dot center"></div><div class="dot dbottom dright"></div></div><div class="side right inner"></div><div class="side left"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side left inner"></div><div class="side bottom"><div class="dot center"></div><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side bottom inner"></div><div class="side back"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div><div class="dot center dleft"></div><div class="dot center dright"></div></div><div class="side back inner"></div><div class="side cover x"></div><div class="side cover y"></div><div class="side cover z"></div></div></div></div></div>
	<div class="main">
		<div class="col-md-6 col-sm-12">
			<div class="login-form">
				<form autocomplete="off" onsubmit="return login();" id="login_form">
					<div class="m-3">
						<div class="mobile_dot">
							<div class="alert alert-dismissible" id="alert_box" style="visibility: hidden;">
								<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.visibility='hidden';"></button>
								<div id="text_alert"></div>
							</div>
						</div>
						<div class="mb-3">
							<h1>Clever - <?= t('login_login') ?></h1>
						</div>
						<div id="fields">
							<div class="mb-3">
								<label for="username" class="form-label"><strong><?= t('login_username'); ?>:</strong></label>
								<input type="text" autocomplete="username" class="form-control border" name="username" id="username" value="<?php if (isset($_COOKIE['username'])) echo htmlentities($_COOKIE['username'], ENT_QUOTES); ?>" autofocus>
							</div>
							<div class="mb-5">
								<label for="password" class="form-label"><strong><?= t('login_password'); ?>:</strong></label>
								<input type="password" autocomplete="current-password" class="form-control border" name="password" id="password">
							</div>
						</div>
						<button type="submit" class="btn btn-black text-white" id="butn_submit"><?= t('login_login'); ?></button>
					</div>
				</form>
				<div class="m-3">
					<div class="mt-5" style="text-align: right;">
						<a onclick="document.getElementById('myModal').style.display = 'block';" href="#" class="link"><?= t('login_reset_password'); ?></a>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo sprintf(t('login_register'), "<a href=\"".$config->get('url')."/register\" class=\"link\">", "</a>"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">

	function login() {
		document.getElementById("fields").classList.remove("invalid");
		var e = document.getElementById("alert_box");
		e.style.visibility='hidden';
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4) {
				if (this.status == 200) {
					result = JSON.parse(this.responseText);
					if (result['succes'] == true) {
						window.location.href = "/home";
					}
					else {
						if (result['reason'] == 'credentials') {
							document.getElementById("fields").classList.add("invalid");
							document.getElementById('password').focus();
						}
						e.classList.add("alert-warning");
						document.getElementById("text_alert").innerHTML = result['message'];
						e.style.visibility='visible';
					}
				}
				else {
					e.classList.add("alert-danger");
					document.getElementById("text_alert").innerHTML = "<?= t('unexpected_error'); ?>";
					e.style.visibility='visible';
				}
			}
		}
		var data = new FormData(document.getElementById('login_form'));
		document.getElementById('password').value = '';
		xhttp.open("POST", "<?= $config->get('url'); ?>/call/login/login.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(data);

		return false;
	}

</script>
<script src="<?= $config->get('url'); ?>/ressources/js/app.js?v=<?= $config->get('version'); ?>"></script>
</body>
</html>