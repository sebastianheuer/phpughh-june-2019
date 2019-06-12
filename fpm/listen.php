<?php
require __DIR__ . '/vendor/autoload.php';

$factory = new Factory();
$factory->createEventListener()->listen();
