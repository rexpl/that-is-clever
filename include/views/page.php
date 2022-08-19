<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8"><title>Clever</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" type="image/x-icon" href="<?= $config->get('url') ?>/ressources/img/logo.png">

	<link href="<?= $config->get('url') ?>/ressources/css/clever.min.css?v=<?= $config->get('version') ?>" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="<?= $config->get('url') ?>/ressources/js/app.min.js?v=<?= $config->get('version') ?>"></script>
</head>
<body class="bg-dark" style="color: white;">
	<div class="mb-5 home-margin-top" style="text-align: center;font-family: 'PermanentMarker';">
		<h1 class="home-display" onclick="window.location.href = '<?= $config->get('url') ?>/home'"><img src="/ressources/img/dices-color-l.png" class="home-dice">&ensp;Clever&ensp;<img src="/ressources/img/dices-color-r.png" class="home-dice"></h1>
	</div>