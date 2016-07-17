<?php

use \PhpAmqpLib\Message\AMQPMessage;
use \PhpAmqpLib\Connection\AMQPConnection;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Monolog\Handler\AmqpHandler;

return array(
    "amqp-config" => function ($c) {
        $amqpCfg = new stdClass;
        $amqpCfg->host = "rabbitmq";
        $amqpCfg->port = 5672;
        $amqpCfg->user = "guest";
        $amqpCfg->pass = "guest";
        $amqpCfg->exchange_name = "local.queue.events";
        $amqpCfg->timeout = 5;
        return $amqpCfg;
    },

    "amqp-connection" => function ($c) {
        $config = $c['amqp-config'];
        $slept = 0;
        while (1) {
            try {
                $conn = new \PhpAmqpLib\Connection\AMQPConnection($config->host, $config->port, $config->user, $config->pass);
                return $conn;
            } catch (\PhpAmqpLib\Exception\AMQPRuntimeException $exception) {
                if ($slept >= $config->timeout) {
                    throw $exception;
                }
                echo "$slept\n";
                $slept += 1;
                sleep(1);
            }
        }
    },
    "queue-driver" => function ($c) {
        return new \BunnyAcme\Queue\Driver\AMQPDriver($c);
    },
    "queue-manager" => function ($c) {
        return new \BunnyAcme\Queue\QueueManager($c);
    },
    "queues-list" => function ($c) {
        return array(
            'sleep' => array(
                new \BunnyAcme\Queue\Workers\SleepyWorker($c)
            )
        );
    },

);
        