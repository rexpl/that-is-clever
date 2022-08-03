<?php 

require dirname(__DIR__) . '/vendor/autoload.php';

?><!DOCTYPE html>
<html lang="<?= lang() ?>">
<head>
	<meta charset="utf-8"><title>Clever - <?= t('register_register') ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php hreflang($config); ?>

	<link rel="icon" type="image/x-icon" href="<?= $config->get('url') ?>/ressources/favicon.ico">

	<link href="<?= $config->get('url') ?>/ressources/css/login.min.css?v=<?= $config->get('version') ?>" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?= $config->get('url') ?>/ressources/js/app.min.js?v=<?= $config->get('version') ?>"></script>

	<style type="text/css">
		body {
			font-family: "Lato", sans-serif;
		}
	</style>
</head>
<body>
	<div class="sidenav"><div class="login-main-text"><div id="wrapper_codepen"><div id="dice_codepen"><div class="side front"><div class="dot center"></div></div><div class="side front inner"></div><div class="side top"><div class="dot dtop dleft"></div><div class="dot dbottom dright"></div></div><div class="side top inner"></div><div class="side right"><div class="dot dtop dleft"></div><div class="dot center"></div><div class="dot dbottom dright"></div></div><div class="side right inner"></div><div class="side left"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side left inner"></div><div class="side bottom"><div class="dot center"></div><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div></div><div class="side bottom inner"></div><div class="side back"><div class="dot dtop dleft"></div><div class="dot dtop dright"></div><div class="dot dbottom dleft"></div><div class="dot dbottom dright"></div><div class="dot center dleft"></div><div class="dot center dright"></div></div><div class="side back inner"></div><div class="side cover x"></div><div class="side cover y"></div><div class="side cover z"></div></div></div></div></div>
	<div class="main">
		<div class="col-md-6 col-sm-12">
			<div class="login-form">
				<form autocomplete="on" onsubmit="RegisterPage.submit();return false;" id="register_form">
					<div class="m-3">
						<div class="mobile_dot">
							<div class="alert alert-dismissible" id="alert_box" style="visibility: hidden;">
								<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.visibility='hidden';"></button>
								<div id="text_alert"></div>
							</div>
						</div>
						<div class="mb-3">
							<h1>Clever - <?= t('register_register') ?></h1>
						</div>
						<div id="fields">
							<div class="mb-3">
								<label for="username" class="form-label"><strong><?= t('login_username') ?>:</strong></label>
								<input type="text" autocomplete="username" class="form-control border" name="username" id="username" onfocus="show('message_username');RegisterPage.ValidateUsername();" onfocusout="hide('message_username')" onkeyup="RegisterPage.ValidateUsername();">
								<div id="message_username" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-3">
								<label for="password" class="form-label"><strong><?= t('login_password') ?>:</strong></label>
								<input type="password" autocomplete="new-password" class="form-control border" name="password" id="password" onfocus="show('message_password');RegisterPage.ValidatePassword();" onfocusout="hide('message_password')" onkeyup="RegisterPage.ValidatePassword();">
								<div id="message_password" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-3">
								<label for="password_confirm" class="form-label"><strong><?= t('register_confirm_password'); ?>:</strong></label>
								<input type="password" autocomplete="new-password" class="form-control border" name="password_confirm" id="password_confirm" onfocus="show('message_password_confirm');RegisterPage.ValidatePasswordConfirm();" onfocusout="hide('message_password_confirm')" onkeyup="RegisterPage.ValidatePasswordConfirm();">
								<div id="message_password_confirm" class="mt-1" style="visibility: hidden;"></div>
							</div>
							<div class="mb-5">
								<label for="mail" autocomplete="email" class="form-label"><strong><?= t('register_mail') ?>:</strong></label>
								<input type="email" class="form-control border" name="mail" id="mail">
							</div>
						</div>
						<button type="submit" class="btn btn-black text-white" id="butn_submit"><?= t('register_register') ?></button>
					</div>
				</form>
				<div class="m-3">
					<ul class="list-inline mt-5" style="text-align: right;">
						<li class="list-inline-item"><?php echo sprintf(t('register_login'), "<a href=\"" . $config->get('url') . "/" . lang() . "/login\" class=\"link\">", "</a>"); ?></li>
					</ul>
				</div>
				<div class="m-3">
					<ul class="list-inline" style="text-align: right;">
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/">Clever</a></li>
						<li class="list-inline-item">⋅</li>
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/login"><?= t('login_login') ?></a>
						<li class="list-inline-item">⋅</li>
						<li class="list-inline-item"><a href="<?= $config->get('url') ?>/<?= lang() ?>/reset-password" class="link"><?= t('login_reset_password'); ?></a></li>
					</ul>
				</div>
			</div>
		</div>
		<?php require dirname(__DIR__) . '/include/views/language-menu.php'; ?>
	</div>
<script type="text/javascript">

	function hide(argument) {
		var e = document.getElementById(argument);
		e.style.visibility = 'hidden';
		e.innerHTML = '';
	}

	function show(argument) {
		var e = document.getElementById(argument);
		e.style.visibility = 'visible';
	}

	const text = {

		textRegister: "<?= t('register_register'); ?>",
		textErrorSomething: "<?= t('register_error_submit') ?>",
		textUsernameExist: "<?= t('register_username_match') ?>",
		textUsernameNotExist: "<?= t('register_username_no_match') ?>",
		textUsernameCharacterMin: "<?= t('register_username_character') ?>",
		textUsernameCharacterMax: "<?= t('register_username_character_max') ?>",
		textUnexpectedError: "<?= t('unexpected_error') ?>",
		textPasswordCharacter: "<?= t('register_character_password') ?>",
		textPasswordCharacterValid: "<?= t('register_valid_password') ?>",
		textPasswordCharacterLow: "<?= t('register_character_password_low') ?>",
		textPasswordCharacterUp: "<?= t('register_character_password_up') ?>",
		textPasswordCharacterNum: "<?= t('register_character_password_num') ?>",
		textPasswordCharacterSpecial: "<?= t('register_character_password_spe') ?>",
		textPasswordCharacterCount: "<?= t('register_character_password_count') ?>",
		textPasswordConfirmValid: "<?= t('register_valid_password_confirm') ?>",
		textPasswordConfirmNotValid: "<?= t('register_password_no_match') ?>",
		
	}

	const RegisterPage = new Register("<?= $config->get('url') ?>",  text);

	hideLanguageChoiceBannerIfCookie();

</script>
</body>
</html>