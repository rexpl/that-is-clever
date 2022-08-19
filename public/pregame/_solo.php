<?php 

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require dirname(__DIR__, 2) . '/include/views/page.php';

?>
	<div class="container home-margin-top">
		<div class="clearfix mt-4">
			<span class="float-start"><button class="btn btn-secondary" onclick="window.location.href = '/home'"><img src="/ressources/img/arrow-left.svg"></button></span>
		</div>
		<div class="m-5 p-5">
			<div class="alert alert-dismissible mb-5" id="alert_box" style="display: none;">
				<button type="button" class="btn-close" onclick="document.getElementById('alert_box').style.display='none';"></button>
				<div id="alert_text"></div>
			</div>
			<div class="center">
				<div id="img">
					<img src="/ressources/img/dices.png" height="100" width="auto">
				</div>
				<div class="mt-5" id="text">
					<button class="btn btn-secondary py-2 px-5" onclick="solo.create();" id="enter_button"><h3><?= t('game_start_solo') ?></h3></button>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">

	text = {

		textUnexpectedError: '<?= t('unexpected_error') ?>',
		buttonText: '<h3><?= t('game_start_solo') ?></h3>',

	}

	const solo = new PregameSolo('<?= $config->get('url') ?>', text);

</script>
</body>
</html>