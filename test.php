<?php

use Mexenus\Database\Database;
use Mexenus\Database\Models\Game;

require 'vendor/autoload.php';

$database = new Database('localhost', 'clever', 'sammy', 'password');

$game = new Game($database);

echo PHP_EOL;

$var = $game->find(1);

$var->status = 3;

$var->save();


$test = [
	'id' => 85,
	'data' => $var,
];

echo json_encode($var, JSON_PRETTY_PRINT);

echo PHP_EOL;
echo PHP_EOL;