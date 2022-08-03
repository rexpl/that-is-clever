<?php 

require dirname(__DIR__) . '/vendor/autoload.php';


if ($_SESSION['cookie_login']) {
	header('Location: /settings');
	die();
}

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
				<h4><?= t('settings_account_password'); ?>:</h4><hr style="height:2.5px;" />
				<div class="py-2 px-3">
					<div class="my-2">
						<div class="alert alert-dismissible" id="alert_box" style="display: none;">
							<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
							<div id="text_alert"></div>
						</div>
					</div>
					<p id="main_text"><?= t('settings_change_password_message') ?></p>
					<form method="post" class="mt-4" autocomplete="on" onsubmit="return false;" id="new_mail_form">
						<div class="mb-3">
							<label for="mail" class="form-label"><strong><?= t('settings_mail_password'); ?>:</strong></label>
							<input type="password" class="form-control form-width" name="password" id="password" autocomplete="current-password">
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= t('settings_change_password_new') ?>:</strong></label>
							<input type="password" class="form-control form-width" name="new_password" id="new_password" autocomplete="new-password">
							<div id="message_password" class="mt-1" style="display: none;"></div>
						</div>
						<div class="mb-3">
							<label for="password" class="form-label"><strong><?= t('settings_change_password_confirm') ?>:</strong></label>
							<input type="password" class="form-control form-width" name="confirm_password" id="confirm_password" autocomplete="new-password">
							<div id="message_confirm_password" class="mt-1" style="visibility: hidden;">The two passwords don't match</div>
						</div>
					</form>
				</div>
				<hr/>
				<div class="float-sm-end">
					<button class="btn btn-secondary" form="new_password_form"><?= t('settings_mail_save') ?></button>
					<button class="btn btn-outline-secondary"><?= t('settings_mail_cancel') ?></button>
				</div>
			</div>
		</div>
	</div>
</body>
</html>