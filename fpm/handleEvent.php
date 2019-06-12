<?php

$redis = new Redis();
$redis->connect('redis');

sleep(random_int(0, 3));

if(random_int(1, 20) === 9) {
    error_log('FAILED');
    exit;
}

$redis->incr('ProcessedEvents');

echo 'OK';
