<?php 

use Clever\Library\Encryption;
use Clever\Library\Model\User;

require dirname(__DIR__) . '/vendor/autoload.php';


if ($_SESSION['cookie_login']) {
	header('Location: /settings');
	die();
}

$user = new User($database);
$user = $user->find($_SESSION['id_user']);

$crypto = new Encryption($_SESSION['personnal_key']);

require dirname(__DIR__) . '/include/views/page.php';


?>
	<div class="container home-margin-top">
		<div class="home-margin-top" style="text-align: center;">
			<h1><?= t('home_settings') ?></h1>
		</div>
		<div class="clearfix mt-4">
			<span class="float-start"><a class="btn btn-secondary" href="<?= $config->get('url') ?>/settings"><img src="<?= $config->get('url') ?>/ressources/img/arrow-left.svg"></a></span>
		</div>
		<div class="mt-5">
			<div class="mb-4 mx-3">
				<h4><?= t('settings_account_mail') ?>:</h4><hr style="height:2.5px;" />
				<div class="py-2 px-3">
					<div class="my-2">
						<div class="alert alert-dismissible" id="alert_box" style="display: none;">
							<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
							<div id="text_alert">HEy</div>
						</div>
					</div>
					<p id="main_text"><?= sprintf(t('settings_mail_message'), e($crypto->decryptString($user->mail))) ?></p>
					<form method="post" class="mt-4" autocomplete="on" onsubmit="return MailSettingPage.submit();return false;" id="new_mail_form">
						<div class="mb-3">
							<label for="mail" class="form-label"><strong><?= t('settings_mail_new') ?>:</strong></label>
							<input type="text" class="form-control form-width" name="email" id="mail" onfocus="show()" onfocusout="hide()" onkeyup="MailSettingPage.ValidateEmail();" autocomplete="email">
							<div id="message_mail" class="mt-1" style="display: none;"></div>
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= t('settings_mail_password') ?>:</strong></label>
							<input type="password" class="form-control form-width" name="password" id="password" autocomplete="current-password">
						</div>
					</form>
				</div>
				<hr/>
				<div class="float-sm-end">
					<button class="btn btn-secondary" type="submit" form="new_mail_form" id="butn_submit"><?= t('settings_mail_save') ?></button>
					<button class="btn btn-outline-secondary" onclick="document.getElementById('new_mail_form').reset()"><?= t('settings_mail_cancel') ?></button>
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

	text = {

		mailValid: "<?= t('register_valid_mail') ?>",
		mailNotValid: "<?= t('register_no_valid_mail') ?>",
		textUnexpectedError: "<?= t('unexpected_error') ?>",
		buttonText: "<?= t('settings_mail_save') ?>",

	}

	const MailSettingPage = new SettingsMail("<?= $config->get('url') ?>" ,text);
</script>
</body>
</html>