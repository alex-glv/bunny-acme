<?php

/**
 * @author Aleksandr Guljajev <gulj.aleks@gmail.com>
 */

namespace App\Commands;

use Monolog\Handler\MissingExtensionException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddQueueItems extends Command {
    /**
     * @var \Pimple\Container
     */
    protected $container;

    /**
     * @param string $name
     * @param \Pimple\Container $container
     */
    public function __construct($name, $container) {
        parent::__construct($name);
        $this->container = $container;
 
    }
    
    /**
     * 
     * @param \Pimple\Container $container
     */
    protected function configure()
    {
        $this
            ->setName('dev:push')
            ->setDescription('Push items to the queue')
            ->addArgument("rate", InputArgument::OPTIONAL, "Message rate", 1)
            ;
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $driver = $this->container['queue-manager']->getDriver();
        $counter = 0;
        while (1) {
            for($count = 0; $count < rand(1, 100); $count++) {
                $counter += 1;
                $payload = serialize(array('message' => "Hello ${counter} times!"));
                $driver->sendMessage('sleep', $payload);
            }
            sleep(1);
        }
    }
}