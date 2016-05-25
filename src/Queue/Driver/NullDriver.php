<?php

namespace BunnyAcme\Queue\Driver;

class NullDriver implements Driver {

    public function __construct($container) {
        ;
    }

    public function sendMessage($queue, $payload) {
        return true;
    }

    public function listen($queue, $workers) {
        return true;
    }
}