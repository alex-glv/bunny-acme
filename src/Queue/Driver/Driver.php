<?php

namespace BunnyAcme\Queue\Driver;

interface Driver {
    public function __construct($container);
    public function sendMessage($queue, $payload);
    public function listen($queue, $workers);
}