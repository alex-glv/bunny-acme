<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LogPrinter extends Command
{
               
    /**
     * @var \Pimple\Container
     */
    protected $container;
    
    public function __construct($name = null, \Pimple\Container $container) {
        parent::__construct($name);
        $this->container = $container;
    }
    
    protected function configure()
    {
        $this
            ->setName('dev:logs')
            ->setDescription('Print logs from queues')
            
            ;
        
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        sleep(5);
        set_error_handler(function ($errno, $errstr, $errfile, $errline){});
               
        $callback = function (\PhpAmqpLib\Message\AMQPMessage $msg) use ($output) {
            $log = $msg->body;
            $output->writeln($log);
        };

        $connection = $this->container['amqp-connection'];
        $channel = $connection->channel();

        register_shutdown_function(function ($ch, $conn) {
            $ch->close();
            $conn->close();
        }, $channel, $connection);

        $channel->exchange_declare('local.queue.log', 'fanout', false, false, false);
        
        list($queue_name, ,) = $channel->queue_declare("", false, false, true, false);
        $channel->queue_bind($queue_name, 'local.queue.log');
        $channel->basic_consume('', '', false, true, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }
                        
    }

        
}