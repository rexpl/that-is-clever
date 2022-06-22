<?php 

require '/project/clever/vendor/autoload.php';

if (verify_login()) {
	header('Location: /home');
	die();
}

?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever - <?= TEXT['login_login']; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<link rel="icon" type="image/x-icon" href="/ressources/img/logo.png">
	<style type="text/css">
		body {
			font-family: "Lato", sans-serif;
		}

		.main-head{
			height: 150px;
			background: #FFF;
		   
		}

		.sidenav {
			height: 100%;
			background-color: #000;
			overflow-x: hidden;
			padding-top: 20px;
		}

		.main {
			padding: 0px 10px;
		}

		@media screen and (max-height: 450px) {
			.sidenav {padding-top: 15px;}
		}

		@media screen and (max-width: 600px) {
			.login-form{
				margin-top: 2%;
			}

			.login-main-text {
				margin-top: 15%;
				padding-bottom: 250px;
				color: #fff;
			}
		}

		@media screen and (min-width: 768px){
			.main{
				margin-left: 40%; 
			}

			.sidenav{
				width: 40%;
				position: fixed;
				z-index: 1;
				top: 0;
				left: 0;
			}

			.login-form{
				margin-top: 60%;
			}

		}

		@media screen and (min-width:  600px) {
			.login-main-text{
				margin-top: 50%;
				padding: 60px;
				color: #fff;
			}

			div.mobile_dot {
				min-height: 5em;
			}
		}

		.login-main-text h2{
			font-weight: 300;
		}

		.btn-black{
			background-color: #000 !important;
			color: #fff;
		}

		@keyframes spin {
			0% { transform: translateZ(-100px) rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
			16% { transform: translateZ(-100px) rotateX(180deg) rotateY(180deg) rotateZ(0deg); }
			33% { transform: translateZ(-100px) rotateX(360deg) rotateY(90deg) rotateZ(180deg); }
			50% { transform: translateZ(-100px) rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
			66% { transform: translateZ(-100px) rotateX(180deg) rotateY(360deg) rotateZ(270deg); }
			83% { transform: translateZ(-100px) rotateX(270deg) rotateY(180deg) rotateZ(180deg); }
			100% { transform: translateZ(-100px) rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
		}
		@keyframes spin-duplicate {
			0% { transform: translateZ(-100px) rotateX(0deg) rotateY(0deg) rotateZ(0deg); }
			16% { transform: translateZ(-100px) rotateX(180deg) rotateY(180deg) rotateZ(0deg); }
			33% { transform: translateZ(-100px) rotateX(360deg) rotateY(90deg) rotateZ(180deg); }
			50% { transform: translateZ(-100px) rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
			66% { transform: translateZ(-100px) rotateX(180deg) rotateY(360deg) rotateZ(270deg); }
			83% { transform: translateZ(-100px) rotateX(270deg) rotateY(180deg) rotateZ(180deg); }
			100% { transform: translateZ(-100px) rotateX(360deg) rotateY(360deg) rotateZ(360deg); }
		}
		@keyframes roll {
			0% { transform: translate3d(-200px,-50px,-400px) }
			12% { transform: translate3d(0px,0,-100px) }
			25% { transform: translate3d(200px,-50px,-400px) }
			37% { transform: translate3d(0px,-100px,-800px) }
			50% { transform: translate3d(-200px,-50px,-400px) }
			62% { transform: translate3d(0px,0,-100px) }
			75% { transform: translate3d(200px,-50px,-400px) }
			87% { transform: translate3d(0px,-100px,-800px) }
			100% { transform: translate3d(-200px,-50px,-400px) }
		}
		#roll:checked ~ #platform {
			width:200px;
			height:200px;
			transform-style: preserve-3d;
			animation: roll 1.6s infinite linear;
		}
		#wrapper_codepen {
			position: relative;
			width: 200px;
			padding-top: 1px;
			margin: 0 auto;
			perspective: 1200px;
		}
		#dice_codepen span {
			position:absolute;
			margin:100px 0 0 100px;
			display: block;
			font-size: 2.5em;
			padding: 10px;
		}
		#dice_codepen {
			position: absolute;
			width: 200px;
			height: 200px;
			transform-style: preserve-3d;
			animation: spin 50s infinite linear;
		}
		.side {
			position: absolute;
			width: 200px;
			height: 200px;
			background: #fff;
			box-shadow:inset 0 0 40px #ccc;
			border-radius: 40px;
		}
		#dice_codepen .cover, #dice_codepen .inner {
			background: #e0e0e0;
			box-shadow: none;
		}
		#dice_codepen .cover {
			border-radius: 0;
			transform: translateZ(0px);
		}
		#dice_codepen .cover.x {
			transform: rotateY(90deg);
		}
		#dice_codepen .cover.z {
			transform: rotateX(90deg);
		}
		#dice_codepen .front  {
			transform: translateZ(100px);
		}
		#dice_codepen .front.inner  {
			transform: translateZ(98px);
		}
		#dice_codepen .back {
			transform: rotateX(-180deg) translateZ(100px);
		}
		#dice_codepen .back.inner {
			transform: rotateX(-180deg) translateZ(98px);
		}
		#dice_codepen .right {
			transform: rotateY(90deg) translateZ(100px);
		}
		#dice_codepen .right.inner {
			transform: rotateY(90deg) translateZ(98px);
		}
		#dice_codepen .left {
			transform: rotateY(-90deg) translateZ(100px);
		}
		#dice_codepen .left.inner {
			transform: rotateY(-90deg) translateZ(98px);
		}
		#dice_codepen .top {
			transform: rotateX(90deg) translateZ(100px);
		}
		#dice_codepen .top.inner {
			transform: rotateX(90deg) translateZ(98px);
		}
		#dice_codepen .bottom {
			transform: rotateX(-90deg) translateZ(100px);
		}
		#dice_codepen .bottom.inner {
			transform: rotateX(-90deg) translateZ(98px);
		}
		.dot {
			position:absolute;
			width:46px;
			height:46px;
			border-radius:23px;
			background:#444;
			box-shadow:inset 5px 0 10px #000;
		}
		.dot.center {
			margin:77px 0 0 77px;
		}
		.dot.dtop {
			margin-top:20px;
		}
		.dot.dleft {
			margin-left:134px;
		}
		.dot.dright {
			margin-left:20px;
		}
		.dot.dbottom {
			margin-top:134px;
		}
		.dot.center.dleft {
			margin:77px 0 0 20px;
		}
		.dot.center.dright {
			margin:77px 0 0 134px;
		}

		.invalid {
			outline-color: red;
			-webkit-animation: shake .5s linear;
		}

		@keyframes shake {
			8%, 41% {
				-webkit-transform: translateX(-10px);
			}
			25%, 58% {
				-webkit-transform: translateX(10px);
			}
			75% {
				-webkit-transform: translateX(-5px);
			}
			92% {
				-webkit-transform: translateX(5px);
			}
			0%, 100% {
				-webkit-transform: translateX(0);
			}
		}

		a.link {
			color: grey;
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
					<p class="my-5"><?= TEXT['message_reset_password']; ?></p>
				</div>
				<div class="m-3 mx-auto">
					<button onclick="window.location.href = '/register';" class="btn btn-secondary"><?= TEXT['register_register']; ?></button>
				</div>
			</div>
		</div>
	</div>
	<div class="sidenav">
		<div class="login-main-text">
			<div id="wrapper_codepen">
				<div id="dice_codepen">
					<div class="side front">
						<div class="dot center"></div>
					</div>
					<div class="side front inner"></div>
					<div class="side top">
						<div class="dot dtop dleft"></div>
						<div class="dot dbottom dright"></div>
					</div>
					<div class="side top inner"></div>
					<div class="side right">
						<div class="dot dtop dleft"></div>
						<div class="dot center"></div>
						<div class="dot dbottom dright"></div>
					</div>
					<div class="side right inner"></div>
					<div class="side left">
						<div class="dot dtop dleft"></div>
						<div class="dot dtop dright"></div>
						<div class="dot dbottom dleft"></div>
						<div class="dot dbottom dright"></div>
					</div>
					<div class="side left inner"></div>
					<div class="side bottom">
						<div class="dot center"></div>
						<div class="dot dtop dleft"></div>
						<div class="dot dtop dright"></div>
						<div class="dot dbottom dleft"></div>
						<div class="dot dbottom dright"></div>
					</div>
					<div class="side bottom inner"></div>
					<div class="side back">
						<div class="dot dtop dleft"></div>
						<div class="dot dtop dright"></div>
						<div class="dot dbottom dleft"></div>
						<div class="dot dbottom dright"></div>
						<div class="dot center dleft"></div>
						<div class="dot center dright"></div>
					</div>
					<div class="side back inner"></div>
					<div class="side cover x"></div>
					<div class="side cover y"></div>
					<div class="side cover z"></div>
				</div>
			</div>
		</div>
	</div>
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
							<h1>Clever - <?= TEXT['login_login']; ?></h1>
						</div>
						<div id="fields">
							<div class="mb-3">
								<label for="username" class="form-label"><strong><?= TEXT['login_username']; ?>:</strong></label>
								<input type="text" autocomplete="username" class="form-control border" name="username" id="username" value="<?php if (isset($_COOKIE['username'])) echo htmlentities($_COOKIE['username'], ENT_QUOTES); ?>">
							</div>
							<div class="mb-5">
								<label for="password" class="form-label"><strong><?= TEXT['login_password']; ?>:</strong></label>
								<input type="password" autocomplete="current-password" class="form-control border" name="password" id="password">
							</div>
						</div>
						<button type="submit" class="btn btn-black" id="butn_submit"><?= TEXT['login_login']; ?></button>
					</div>
				</form>
				<div class="m-3">
					<div class="mt-5" style="text-align: right;">
						<a onclick="document.getElementById('myModal').style.display = 'block';" href="#" class="link"><?= TEXT['login_reset_password']; ?></a>&nbsp;&nbsp;-&nbsp;&nbsp;<?php echo sprintf(TEXT['login_register'], "<a href=\"/register\" class=\"link\">", "</a>"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<script type="text/javascript">
	document.getElementById('username').focus();

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
					document.getElementById("text_alert").innerHTML = "<?= TEXT['unexpected_error']; ?>";
					e.style.visibility='visible';
				}
			}
		}
		var data = new FormData(document.getElementById('login_form'));
		document.getElementById('password').value = '';
		xhttp.open("POST", "/call/login/login.php", true);
		xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
		xhttp.send(data);

		return false;
	}
</script>
</body>
</html>