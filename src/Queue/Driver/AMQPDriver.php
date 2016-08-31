<?php

namespace BunnyAcme\Queue\Driver;
use BunnyAcme\Queue\Workers\Worker;

class AMQPDriver implements Driver {

    /**
     * @var \PhpAmqpLib\Connection\AMQPConnection
     */
    protected $connection;
    protected $workersMap;
    protected $container;
    /** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
    protected $channel;
    
    public function __construct($container) {
        $connection = $container['amqp-connection'];
        $amqpConfig = $container['amqp-config'];
        $this->exchangeName = $amqpConfig->exchange_name;
        if (!($connection instanceof \PhpAmqpLib\Connection\AMQPConnection)) {
            throw new \RuntimeException(sprintf("Expected \\PhpAmqpLib\\Connection\\AMQPConnection, got %s", get_class($connection)));
        }
        $this->connection = $connection;
        $this->container = $container;

        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->exchangeName, 'direct', false, true, false);
    }

    public function sendMessage($queue, $payload) {
        // $logger = $this->container['logger'];
        // $logger->addInfo("Sending message over queue {queue}",
        // array("payload" => $payload, "queue" => $queue));
        $message = new \PhpAmqpLib\Message\AMQPMessage($payload, array('delivery_mode' => 2));
        $this->channel->basic_publish($message, $this->exchangeName, $queue, true);
    }

    public function listen($queue, $workers) {
        $queueDeclareTitle = substr(sprintf("BunnyAcme.events.%s", $queue), 0, 255);
        list($autoQueueName,, ) = $this->channel->queue_declare($queueDeclareTitle, false, true, false, false);
        $this->channel->queue_bind($autoQueueName, $this->exchangeName, $queue);
        $logger = $this->container['logger'];
        $channel = $this->channel;
        /** @var Worker $worker */
        foreach ($workers as $worker) {
            if (!($worker instanceof Worker)) {
                throw new \Exception(sprintf("Class %s is not instance of Worker interface", get_class($worker)));
            }
            $this->channel->basic_consume($autoQueueName, '', false, false, false, false, function($payload) use ($worker, $channel, $queue, $logger) {
                try {
                    $logger->addInfo("Received item over {queue} queue", array("message" => $payload, "queue" => $queue));
                    $worker->handleJob($payload->body);
                } catch (\Exception $e) {
                    // log message
                    $logger->addCritical('Exception when executing the job: {msg}', array('msg' => $e->getMessage(), 'payload' => $payload));
                    return false;
                }
                $channel->basic_ack($payload->delivery_info['delivery_tag']);
            }
            );
        }
        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function __destruct() {
        // $logger = $this->container['logger'];
        // $logger->addInfo("Calling __destruct on AMQPDriver");
        try {
            if ($this->channel) {
                $this->channel->close();
            }
        } catch (\Exception $e) {
            // $logger->addCritical("Exception: failed to close channel and connection. Msg {msg}", array('msg' => $e->getMessage()));
        }
    }
}
