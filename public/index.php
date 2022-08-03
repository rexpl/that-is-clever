<?php

require dirname(__DIR__) . '/vendor/autoload.php';

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"><title>Clever</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<meta name="description" content="Some tags are vital for SEO. Others have little or no impact on rankings. Here's every type of meta tag you need to know about.The purpose of a meta description is to reflect the essence of a page, but with more details and context."/>

	<?php hreflang($config); ?>

	<link rel="icon" type="image/x-icon" href="<?= $config->get('url') ?>/ressources/favicon.ico">

	<link href="<?= $config->get('url') ?>/ressources/css/login.min.css?v=<?= $config->get('version') ?>" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?= $config->get('url') ?>/ressources/js/app.min.js?v=<?= $config->get('version') ?>"></script>

	<style type="text/css">
		body {
			font-family: "Lato", sans-serif;
			color: white;
		}

		.masthead {
			position: relative;
			background-color: #343a40;
			background: url("../ressources/img/bg-masthead.jpg") no-repeat center center;
			background-size: cover;
		}

		header.masthead {
			padding-top: 8rem;
			padding-bottom: 16rem;
		}

		.btn-primary.custom-btn {
			background-color: #a442f5;
			border-color: #a442f5;
		}
	</style>
</head>
<body>
	<nav class="navbar-dark bg-dark static-top py-2">
		<div class="container clearfix">
			<div class="float-start pt-1">
				<a class="navbar-brand" href="<?= $config->get('url') ?>/<?= lang() ?>/">Clever</a>
			</div>
			<div class="float-end">
				<a class="btn btn-primary" href="<?= $config->get('url') ?>/<?= lang() ?>/login"><?= t('login_login') ?></a>
				<a class="btn btn-primary custom-btn" href="<?= $config->get('url') ?>/<?= lang() ?>/register"><?= t('register_register') ?></a>
			</div>
		</div>
	</nav>
	<header class="masthead">
		<div class="container position-relative">
			<div class="row justify-content-center">
				<div class="col-xl-6">
					<div class="text-center text-white">
						<h1 class="mb-5">Clever</h1>
					</div>
				</div>
			</div>
		</div>
	</header>
	<section class="features-icons bg-dark text-center">
		<div class="container">
			<div class="row">
				<div class="col-lg-4">
					<div class="features-icons-item mx-auto m-5">
						<div class="features-icons-icon d-flex mb-3">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user" style="display: block;margin: auto;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						</div>
						<h3><?= t('home_solo') ?></h3>
						<p class="lead m-5">This theme will look great on any device, no matter the size!</p>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="features-icons-item mx-auto m-5">
						<div class="features-icons-icon d-flex mb-3">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users" style="display: block;margin: auto;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
						</div>
						<h3><?= t('home_multiplayer') ?></h3>
						<p class="lead m-5">Featuring the latest build of the new Bootstrap 5 framework!</p>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="features-icons-item mx-auto m-5">
						<div class="features-icons-icon d-flex mb-3">
							<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-smile" style="display: block;margin: auto;"><circle cx="12" cy="12" r="10"></circle><path d="M8 14s1.5 2 4 2 4-2 4-2"></path><line x1="9" y1="9" x2="9.01" y2="9"></line><line x1="15" y1="9" x2="15.01" y2="9"></line></svg>
						</div>
						<h3><?= t('home_play_friend') ?></h3>
						<p class="lead m-5">Ready to use with your own content, or customize the source files!</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="bg-dark">
		<div class="container-fluid p-0">
			<div class="row g-0">
				<div class="col-lg-6 order-lg-2 text-white showcase-img" style="background-image: url('../ressources/img/bg-masthead-2.png')"></div>
				<div class="col-lg-6 order-lg-1 my-auto p-5">
				   <div class="my-5">
						<h2>Updated For Bootstrap 5</h2>
						<p class="lead mb-0">Newly improved, and full of great utility classes, Bootstrap 5 is leading the way in mobile responsive web development! All of the themes on Start Bootstrap are now using Bootstrap 5!</p>
					</div>
				</div>
			</div>
			<div class="row g-0">
				<div class="col-lg-6 text-white showcase-img" style="position: relative;background: url('../ressources/img/bg-masthead-3.png') no-repeat;"></div>
				<div class="col-lg-6 my-auto p-5">
					<div class="my-5">
						<h2>Updated For Bootstrap 5</h2>
						<p class="lead mb-0">Newly improved, and full of great utility classes, Bootstrap 5 is leading the way in mobile responsive web development! All of the themes on Start Bootstrap are now using Bootstrap 5!</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<footer class="footer bg-dark py-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-9 h-100 text-center text-lg-start my-auto">
					<ul class="list-inline mb-2">
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/">Clever</a></li>
						<li class="list-inline-item">⋅</li>
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/login"><?= t('login_login') ?></a></li>
						<li class="list-inline-item">⋅</li>
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/register"><?= t('register_register') ?></a></li>
						<li class="list-inline-item">⋅</li>
						<li class="list-inline-item"><a class="link" href="<?= $config->get('url') ?>/<?= lang() ?>/reset-password"><?= t('login_reset_password') ?></a></li>
					</ul>
					<p class="text-muted small mb-4 mb-lg-0">Clever - <?= date("Y") ?></p>
				</div>
				<div class="col-lg-3 h-100 text-center text-lg-end my-auto">
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
					</div>
				</div>
			</div>
		</div>
	</footer>
	<?php require dirname(__DIR__) . '/include/views/language-menu.php'; ?>
<script type="text/javascript">

	hideLanguageChoiceBannerIfCookie();

</script>
</body>
</html>