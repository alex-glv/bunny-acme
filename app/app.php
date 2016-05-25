#!/usr/bin/env php
<?php

require 'bootstrap.php';
$config = require 'config.php';
$container = new \Pimple\Container();

foreach ($config as $key => $cfg) {
    $container[$key] = $cfg;
}

use Symfony\Component\Console\Application;
$application = new Application();

$commands = array(
    new \App\Commands\Listen(null, $container['queue-manager'])
);

foreach ($commands as $command) {
    $application->add($command);
}

$application->run();