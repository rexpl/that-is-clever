<div style="position: relative" id="language-banner">
	<div class="language-banner">
		<div class="float-end border rounded px-5 py-3 bg-white" style="display: inline-block;">
			<div class="input-group">
				<span class="input-group-text">
					<img src="<?= $config->get('url') ?>/ressources/flags/<?= lang() ?>.jpg" height="24px" width="auto">
				</span>
				<select id="language" class="form-select" onchange="window.location.href = '<?= $config->get('url') ?>/' + this.value + '<?= URI_SAFE_LANG ?>';" style="cursor: pointer;">
					<?php 
						foreach ($config->get('supported_lang') as $value) {

							if (lang() == $value) {

								echo "<option value=\"" . $value . "\" selected>" . t('name_for_' . $value) . "</option>";
								continue;	
							}

							echo "<option value=\"" . $value . "\">" . t('name_for_' . $value) . "</option>";
						}
					?>
				</select>
				<button type="button" class="btn-close float-end my-auto ms-3" onclick="hideLanguageChoiceBanner();"></button>
			</div>
		</div>
	</div>
</div>