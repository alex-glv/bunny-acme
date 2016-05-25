<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Listen extends Command
{
    /**
     * @var \BunnyAcme\Queue\QueueManager $queueManager
     */
    protected $queueManager;
    
    /**
     * @var \Pimple\Container
     */
    protected $container;
    
    public function __construct($name = null, \BunnyAcme\Queue\QueueManager $queueManager) {
        parent::__construct($name);
        $this->queueManager = $queueManager;
    }
    
    /**
     * 
     * @param \Pimple\Container $container
     */
    protected function configure()
    {
        $this
            ->setName('queue:executor')
            ->setDescription('Start up a queue executor and listen for incoming events')
            ->addArgument(
                'queue',
                InputArgument::REQUIRED,
                'Queue name to listen to'
            )
            ;
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->listen($input->getArgument('queue'));
            $output->writeln('Attached to queue');
        } catch (Exception $exception) {
            $output->writeln(sprintf("Exception thrown when attaching queue listener: %s", $exception->getMessage()));
        } 
    }

    protected function listen($queueName) 
    {
        /** @var \BunnyAcme\Queue\Driver\Driver $driver */
        $workers = $this->queueManager->getWorkers($queueName);
        $driver = $this->queueManager->getDriver();

        $driver->listen($queueName, $workers);
    }
}