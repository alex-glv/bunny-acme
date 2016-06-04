#!/usr/bin/env php
<?php

date_default_timezone_set('Europe/Amsterdam');


require 'bootstrap.php';
$config = require 'config.php';
$container = new \Pimple\Container();

foreach ($config as $key => $cfg) {
    $container[$key] = $cfg;
}

use Symfony\Component\Console\Application;
$application = new Application();

$commands = array(
    new \App\Commands\Listen(null, $container),
    new \App\Commands\LogPrinter(null, $container)
);

foreach ($commands as $command) {
    $application->add($command);
}

$application->run();