<?php

require __DIR__ . '/vendor/autoload.php';

$redis = new Redis();
$redis->connect('127.0.0.1');

$faker = \Faker\Factory::create();

$amountOfTasks = $argv[1];

echo sprintf("Adding %s events to Redis.\n", (int)$amountOfTasks);
echo "Command:\n";
echo '$redis->xAdd('. "\n";
echo "    'events',\n";
echo "    '*',\n";
echo "    ['type' => 'CustomerAccountCreated', 'payload' => <random email>];\n";
echo ")\n";
$i = 0;
$redis->multi();
while($i < $amountOfTasks) {
    echo '.';
    $payload = ['name' => $faker->email];
    $redis->xAdd('events', '*', ['type' => 'CustomerAccountCreated', 'payload' => json_encode($payload)]);
    $i++;
}
$redis->exec();
echo "\n\033[32mdone\033[0m \n";

