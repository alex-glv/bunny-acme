<?php

namespace BunnyAcme\Queue\Workers;

interface Worker {
    public function __construct($container);
    public function handleJob($payload);
}