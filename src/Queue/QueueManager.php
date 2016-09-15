<?php

namespace BunnyAcme\Queue;

use BunnyAcme\Queue\Workers\Worker;

class QueueManager {
    protected $container;

    /** @var \BunnyAcme\Queue\Driver\Driver $driver */
    protected $driver = null;
    public function __construct($container) {
        $this->container = $container;
        $this->driver = $container['queue-driver'];
    }

    /**
     * @return null
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * @param $queueName
     * @return Worker $worker
     * @throws \Exception
     */
    public function getWorker($queueName) {
        $queueConfiguration = $this->container["queues-list"];
        if (!isset($queueConfiguration[$queueName])) {
            throw new \Exception(sprintf("No workers exist for '%s' queue", $queueName));
        }
        $worker = $queueConfiguration[$queueName];
        return $worker;
    }
}
