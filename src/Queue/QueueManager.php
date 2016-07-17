<?php

namespace BunnyAcme\Queue;

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
     * @return mixed
     * @throws \Exception
     */
    public function getWorkers($queueName) {
        $queueConfiguration = $this->container["queues-list"];
        if (!isset($queueConfiguration[$queueName])) {
            throw new \Exception(sprintf("No workers exist for '%s' queue", $queueName));
        }
        $workersMap = $queueConfiguration[$queueName];
        return $workersMap;
    }
}
