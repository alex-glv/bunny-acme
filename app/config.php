<?php

use \PhpAmqpLib\Message\AMQPMessage;
use \PhpAmqpLib\Connection\AMQPConnection;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\AmqpHandler;

return array(
    "amqp-config" => function ($c) {
        $amqpCfg = new stdClass;
        $amqpCfg->host = "10.0.2.2";
        $amqpCfg->port = 5672;
        $amqpCfg->user = "guest";
        $amqpCfg->pass = "guest";
        $amqpCfg->exchange_name = "local.queue.events";
        return $amqpCfg;
    },
    
    "amqp-connection" => function ($c) {
        $config = $c['amqp-config'];
        return new \PhpAmqpLib\Connection\AMQPConnection($config->host, $config->port, $config->user, $config->pass);
    },
    "queue-driver" => function($c) {
        return new \BunnyAcme\Queue\Driver\AMQPDriver($c);
    },
    "queue-manager" => function($c) {
        return new \BunnyAcme\Queue\QueueManager($c);
    },
    "queues-list" => function($c) {
        return array(      
            'sleep' => array(
                new \BunnyAcme\Queue\Workers\SleepyWorker($c)
            )
        );
    },
     
);
        