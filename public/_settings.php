<?php 

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/include/views/page.php';

?>
	<div class="modal" id="myModal" style="display: none;background-color: rgba(0,0,0,0.3);color: grey;">
		<div class="modal-dialog modal-lg modal-dialog-centered animate__animated animate__zoomIn">
			<div class="modal-content">
				<div class="m-3">
					<button type="button" class="btn-close float-end" onclick="document.getElementById('myModal').style.display = 'none';"></button>
				</div>
				<div class="modal-body mx-auto my-1">
					<div class="mb-5" style="text-align: center;">
						<img src="/ressources/img/key.svg" height="100" width="auto">
					</div>
					<h5><?= t('settings_password_message'); ?></h5>
					<form method="post" class="mt-4" autocomplete="off" onsubmit="SettingPage.verifyPassword();return false;" id="verify_form">
						<input type="text" autocomplete="username" hidden value="<?= e($_SESSION['username']) ?>">
						<input type="password" class="form-control w-75 mx-auto" name="password" id="password" autocomplete="current-password" placeholder="<?= t('settings_password_enter'); ?>">
					</form>
				</div>
				<div class="m-3 mx-auto">
					<button class="btn btn-secondary" type="submit" form="verify_form" id="butn_submit"><?= t('settings_password_verify'); ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="container mt-5">
		<div class="home-margin-top" style="text-align: center;">
			<h1><?= t('home_settings'); ?></h1>
		</div>
		<div class="clearfix mt-4">
			<span class="float-start"><a class="btn btn-secondary" href="<?= $config->get('url') ?>/home"><img src="<?= $config->get('url') ?>/ressources/img/arrow-left.svg"></a></span>
		</div>
		<div class="m-3" id="alert_box" style="display: none;">
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
				<div><?= t('unexpected_error'); ?> <strong id="alert_text"></strong></div>
			</div>
		</div>
		<div class="mt-5">
			<div class="mb-4 mx-3">
				<h4><?= t('settings_application'); ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3">
					<span class="float-start"><?= t('settings_language'); ?>:</span>
					<span class="float-end">
						<select id="language" class="form-select" onchange="language(this.value);" style="cursor: pointer;">
							<?php 
								foreach ($config->get('supported_lang') as $value) {
									echo "<option value=" . $value . ">" . t('name_for_' . $value) . "</option>";
								}
							?>
						</select>
					</span>
				</div>
			</div>
			<div class="mb-4 mx-3">
				<h4><?= t('settings_account'); ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3 link-setting" onclick="SettingPage.click('email');">
					<span class="float-start p-1"><?= t('settings_account_mail'); ?></span>
					<span id="email_loader" class="float-end">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
					</span>
				</div><hr/>
				<div class="clearfix py-2 px-3 link-setting" onclick="SettingPage.click('password');">
					<span class="float-start p-1"><?= t('settings_account_password'); ?></span>
					<span id="password_loader" class="float-end">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
					</span>
				</div>
			</div>
			<div class="mb-4 mx-3">
				<h4><?= t('settings_logout_this'); ?>:</h4><hr style="height:2.5px;" />
				<div class="clearfix py-2 px-3 link-setting" onclick="SettingPage.click('logout');">
					<span class="float-start p-1"><?= t('settings_logout_this'); ?></span>
					<span id="logout_loader" class="float-end">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
					</span>
				</div><hr/>
				<div class="clearfix py-2 px-3 link-setting" onclick="SettingPage.click('logout_all');">
					<span class="float-start p-1"><?= t('settings_logout_everywhere'); ?></span>
					<span id="logout_all_loader" class="float-end">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
					</span>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">

	text = {
		textButton: "<?= t('settings_password_verify'); ?>",
	}

	const SettingPage = new Settings("<?= $config->get('url') ?>", text);

	document.getElementById('language').value = get_cookie('lang');

	function language(argument) {
		
		window.location.href = "<?= $config->get('url') ?>/settings?language=" + argument;
	}
</script>
</body>
</html>