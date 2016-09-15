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
            } catch (ErrorException $e) {
                if ($slept >= $config->timeout) {
                    throw $e;
                }
                $slept += 1;
                sleep(1);
            }
        }
    },
    "queue-driver" => function ($c) {
        return new \BunnyAcme\Queue\Driver\NullDriver($c);
    },
    "queue-manager" => function ($c) {
        return new \BunnyAcme\Queue\QueueManager($c);
    },
    "queues-list" => function ($c) {
        return array(
            'sleep' => new \BunnyAcme\Queue\Workers\SleepyWorker($c)
        );
    },
    "logger" => function($c) {
        $log = new \Monolog\Logger('local.queue.log');
        $handler = new \Monolog\Handler\TestHandler();

        $log->pushHandler($handler);
        # $log->pushProcessor(new \Monolog\Processor\MemoryUsageProcessor());
        $log->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());

        return $log;
    }
);
