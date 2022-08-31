<?php

use Workerman\Worker;

use Clever\Library\Game\Handler;


require 'vendor/autoload.php';


$websocket = new Worker('websocket://127.0.0.1:8000');
$handler = new Handler($config, $websocket);

$websocket->onWorkerStart = [$handler, 'onWorkerStart'];
$websocket->onWebSocketConnect = [$handler, 'onWebSocketConnect'];

$websocket->onMessage = function() {};

Worker::runAll();