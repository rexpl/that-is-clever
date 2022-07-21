<?php 

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/include/views/page.php';

?>
	<div class="container pt-5">
		<div id="nav-home">
			<div class="clever-box my-3 home-box p-3">
				<div class="clearfix">
					<div class="float-start">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> <?= e($_SESSION['username']) ?>
					</div>
					<div class="float-end">
						<a href="<?= $config->get('url') ?>/settings"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg></a>
					</div>
				</div>
			</div>
		</div>
		<div class="row" id="page-home">
			<div class="col-md-4 my-3">
				<div class="clever-box home-box h-100 d-flex py-5" onmouseover="hover_start('img_solo', 'user')" onmouseout="hover_end('img_solo', 'user')" onclick="window.location.href = '/pregame/solo'">
					<h2 class="my-5 py-5 my-auto mx-auto"><img id="img_solo" src="/ressources/img/user.svg" class="home-dice">&ensp;<?= TEXT['home_solo']; ?></h2>
				</div>
			</div>
			<div class="col-md-4 my-3">
				<div class="clever-box home-box h-100 d-flex py-5" onmouseover="hover_start('img_multi', 'users')" onmouseout="hover_end('img_multi', 'users')" style="cursor: default;">
					<h2 class="my-5 py-5 my-auto mx-auto"><img id="img_multi" src="/ressources/img/users.svg" class="home-dice">&ensp;<?= TEXT['home_multiplayer']; ?><span class="badge bg-secondary m-3">Comming soon</span></h2>
				</div>
			</div>
			<div class="col-md-4 my-3">
				<div class="clever-box home-box h-100 d-flex py-5" onmouseover="hover_start('img_frie', 'smile')" onmouseout="hover_end('img_frie', 'smile')" onclick="window.location.href = '/pregame/friend'">
						<h2 class="my-5 py-5 my-auto mx-auto"><img id="img_frie" src="/ressources/img/smile.svg" class="home-dice">&ensp;<?= TEXT['home_play_friend']; ?></h2>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		function hover_start(x, q) {
			document.getElementById(x).src = "/ressources/img/" + q +"-red.svg";
		}

		function hover_end(x, q) {
			document.getElementById(x).src = "/ressources/img/" + q +".svg";
		}
	</script>
</body>
</html>