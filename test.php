<?php

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

require '/var/www/html/that-is-clever/vendor/autoload.php';

$worker = new Worker();
$worker->onWorkerStart = function () {
    // Websocket protocol for client.
    $ws_connection = new AsyncTcpConnection('ws://127.0.0.1:8000');

    $ws_connection->onConnect = function ($connection) {
        echo "Connected.\n";
    };
    $ws_connection->onMessage = function ($connection, $data) {
        echo "Recv: $data\n";
    };
    $ws_connection->onError = function ($connection, $code, $msg) {
        echo "Error: $msg\n";
    };
    $ws_connection->onClose = function ($connection) {
        echo "Connection closed\n";
    };
    $ws_connection->connect();
};

Worker::runAll();