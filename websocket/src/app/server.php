<?php

// load configuration message
$config = parse_ini_file(__DIR__ . '/../config/' . 'env.ini', true);

// client fd collection
$client_fds = [];

// define websocket server
$server = new swoole_websocket_server($config['server']['host'], $config['server']['port']);

// open callback function
$server->on('open', function (swoole_websocket_server $server, $request) use (&$client_fds) {
    // connected successful, then record
    array_push($client_fds,$request->fd);
});

// message callback function
$server->on('message', function (swoole_websocket_server $server, $frame) use (&$client_fds) {
    // frame construct : frame.fd frame.data frame.opcode frame.finish
    // send a broadcast to notify all users
    foreach ($client_fds as $fd) {
        $server->push($fd, $frame->data);
    }
});

// close callback function
$server->on('close', function (swoole_websocket_server $server, $fd) use (&$client_fds) {
    // close session as well as destroy flag fd
    $res = array_search($fd, $client_fds, true);
    unset($client_fds[$res]);
});

// set server runtime params
$server->set($config['runtime']);

// start server as service
$server->start();
