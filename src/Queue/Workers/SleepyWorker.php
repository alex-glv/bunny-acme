<?php

namespace BunnyAcme\Queue\Workers;

class SleepyWorker extends \BunnyAcme\Queue\Workers\AbstractWorker implements Worker {

    /**
     * @param $payload
     * @throws \CommandQueue_CommandNotFoundException
     * @throws \CommandQueue_PlanNotFoundException
     * @throws \Exception
     */
    public function handleJob($payload) {
        $this->container["logger"]->addDebug("Sleepy reporting: {payload}", array("payload" => $payload));
        sleep(1);
        
    }
}

