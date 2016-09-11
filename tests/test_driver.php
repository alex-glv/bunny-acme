<?php
use PHPUnit\Framework\TestCase;


class TestDriver extends TestCase
{
    /** @var \Pimple\Container */
    public $cnt;

    public function __construct() {
        // todo: move to bootstrap
        $cmp = require('app/config-test.php');
        $this->cnt = new \Pimple\Container();

        foreach ($cmp as $key => $cfg) {
            $this->cnt[$key] = $cfg;
        }
        parent::__construct();
    }

    public function testNullDriver() {
        /** @var \BunnyAcme\Queue\QueueManager */
        $qm = $this->cnt['queue-manager'];
        $this->assertTrue($qm->getDriver() instanceof \BunnyAcme\Queue\Driver\NullDriver);
    }
    
}
