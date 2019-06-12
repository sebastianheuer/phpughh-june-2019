<?php

require __DIR__ . '/vendor/autoload.php';

$numberOfTasks = $argv[1];

$tasks = [];
for ($i = 0; $i < $numberOfTasks; $i++) {
    $tasks[] = new \ParallelPhp\Task();
}

$command = new \ParallelPhp\CalculateCommand(new \ParallelPhp\ProgressOutput());
$command->execute($tasks);
