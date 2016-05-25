<?php

namespace BunnyAcme\Queue\Workers;

use BunnyAcme\Queue\Workers\Worker;

class AbstractWorker implements Worker {

    /** @var \Pimple\Container $container */
    protected $container;

    public function __construct($container) {
        $this->container = $container;
        
    }

    public function handleJob($payload) {
        throw new \Exception("Not implemented");
    }
}