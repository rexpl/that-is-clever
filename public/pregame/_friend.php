<?php 

require dirname(__DIR__, 2) . '/vendor/autoload.php';
require dirname(__DIR__, 2) . '/include/views/page.php';

?>
	<div class="container home-margin-top" id="pregame-friend">
		<div class="clearfix mt-4" id="button_back">
			<span class="float-start"><button class="btn btn-secondary" onclick="window.location.href = '/home'"><img src="/ressources/img/arrow-left.svg"></button></span>
		</div>
		<div class="m-5 mx-auto">
			<div class="row">
				<div class="col-md-6">
					<div class="clever-box my-3 mx-2 pt-5 pb-3 home-box h-100">
						<h2 id="title_join"><?= TEXT['game_friend_join_game']; ?></h2>
						<div class="mt-5 mb-3" id="text_join">
							<label class="mb-2" for="gcode"><?= TEXT['game_friend_enter_code']; ?></label>
							<input type="text" class="form-control w-50 mx-auto" name="gcode" id="gcode" maxlength="8" oninput="this.value = this.value.toUpperCase();verify_code(this.value);">
							<div id="message_gcode" class="mt-1" style="visibility: hidden;color: red;"><?= TEXT['game_friend_code_error']; ?></div>
						</div>
						<div class="mb-5" id="btn_join">
							<button type="button" class="btn btn-danger" id="btn-join" onclick="join()" disabled><?= TEXT['game_friend_join']; ?></button>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="clever-box my-3 mx-2 pt-5 pb-3 home-box h-100">
						<h2><?= TEXT['game_friend_create_game']; ?></h2>
						<div class="mt-5 mb-3">
							<p style="text-align: left;padding-left: 25%;">
								<?= TEXT['game_friend_1']; ?><br/>
								<?= TEXT['game_friend_2']; ?><br/>
								<?= TEXT['game_friend_3']; ?>
							</p>
							<div class="mt-5" id="text_create">
								<button type="button" class="btn btn-danger" id="btn-create" onclick="create_game();"><?= TEXT['game_friend_create']; ?></button>
							</div>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div>
<script type="text/javascript">
	var creator = false;

	function create_game() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result = JSON.parse(this.responseText);
				if (result['success'] == true) {
					creator = true;
					document.getElementById('text_create').innerHTML = "<h3><?= TEXT['game_friend_code']; ?> " + result['token'] + "</h3>";
					document.getElementById('button_back').style.visibility = 'hidden';
					document.getElementById('btn_join').style.display = 'none';
					document.getElementById('title_join').innerHTML = "<?= TEXT['game_friend_participants']; ?>";
					player = "<?= TEXT['game_friend_player']; ?>";
					document.getElementById('text_join').innerHTML = "<h4>" + player.replace("-{n}-", "1") + " <span id=\"player_0\"></span></h4><hr/><h4>" + player.replace("-{n}-", "2") + " <span id=\"player_1\"></span></h4><hr/><h4>" + player.replace("-{n}-", "3") + " <span id=\"player_2\"></span></h4><hr/><h4>" + player.replace("-{n}-", "4") + " <span id=\"player_3\"></span></h4>";
					document.getElementById('text_join').style.textAlign = 'left';
					document.getElementById('text_join').style.paddingLeft = '20%';
					document.getElementById('text_join').style.paddingRight = '20%';
					setIntervalAndExecute();
				}
			}
		}
		xhttp.open("GET", "/call/pregame/create_game.php?q=friend", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}

	var gcode;

	function verify_code(argument) {
		document.getElementById('btn-join').disabled = true;

		if (argument.length < 7) {
			document.getElementById('message_gcode').style.visibility = 'hidden';
			return false;
		}

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result = JSON.parse(this.responseText);
				if (result['success'] == true) {
					document.getElementById('message_gcode').style.visibility = 'hidden';
					document.getElementById('btn-join').disabled = false;
					gcode = result['id'];
				}
				else {
					document.getElementById('message_gcode').style.visibility = 'visible';
				}
			}
			else {
				document.getElementById('message_gcode').style.visibility = 'visible';
			}
		}
		xhttp.open("GET", "/call/pregame/verify_code.php?q=" + argument, true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}

	function join() {
		
		document.getElementById('btn-join').disabled = true;
		document.getElementById('btn-create').disabled = true;

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result = JSON.parse(this.responseText);
				if (result['success'] == true) {
					document.getElementById('button_back').style.visibility = 'hidden';
					document.getElementById('btn_join').style.display = 'none';
					document.getElementById('title_join').innerHTML = "<?= TEXT['game_friend_participants']; ?>";
					player = "<?= TEXT['game_friend_player']; ?>";
					document.getElementById('text_join').innerHTML = "<h4>" + player.replace("-{n}-", "1") + " <span id=\"player_0\"></span></h4><hr/><h4>" + player.replace("-{n}-", "2") + " <span id=\"player_1\"></span></h4><hr/><h4>" + player.replace("-{n}-", "3") + " <span id=\"player_2\"></span></h4><hr/><h4>" + player.replace("-{n}-", "4") + " <span id=\"player_3\"></span></h4>";
					document.getElementById('text_join').style.textAlign = 'left';
					document.getElementById('text_join').style.paddingLeft = '20%';
					document.getElementById('text_join').style.paddingRight = '20%';
					setIntervalAndExecute();
				}
			}
		
		}
		xhttp.open("GET", "/call/pregame/join_game.php?q=" + gcode, true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}

	function setIntervalAndExecute() {
		fetchPlayers();
		return(setInterval(fetchPlayers, 3000));
	}

	function fetchPlayers() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				result = JSON.parse(this.responseText);

				if (result === 'start') window.location.href = '/game/friend';

				i = 0;
			
				for (x in result) {
					i++;
					document.getElementById('player_' + x).innerHTML = result[x]['username'];
				}

				if (creator && i > 1) {
					document.getElementById('text_create').innerHTML += "<button type=\"button\" class=\"btn btn-danger\" id=\"btn-create\" onclick=\"startGame();\"><?= TEXT['game_friend_start']; ?></button>";
					creator = false;
				}
			}
		
		}
		xhttp.open("GET", "/call/pregame/fetch_players.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}

	function startGame() {
		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {
			if (this.readyState == 4 && this.status == 200) {
				window.location.href = '/game/friend';			
			}
		}
		xhttp.open("GET", "/call/pregame/start_game.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send();
	}
</script>
</body>
</html>