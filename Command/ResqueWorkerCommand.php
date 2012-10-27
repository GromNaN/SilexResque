<?php

namespace Grom\SilexResque\Command;

use Silex\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Resque_Event;
use Resque_Job;
use Resque_Worker;

class ResqueWorkerCommand extends Command
{
    private $app;

    public function __construct(Application $app = null)
    {
        parent::__construct();
        $this->app;
    }

    protected function configure()
    {
        $this
            ->setName('resque:worker')
            ->setDescription('Run a resque worker')
            ->setDefinition(array(
                new InputArgument('queues', InputArgument::REQUIRED, 'Comma separated list of queues to process'),
                new InputOption('interval', null, InputOption::VALUE_REQUIRED, 'Time between each task (seconds)', 5),
            ))
            ->setHelp(<<<EOT
The <info>{$this->getName()}</info> command register a new Resque worker.

<info>{$this->getName()} [--interval=5] default</info>

EOT
                )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queues = explode(',', $input->getArgument('queues'));
        $interval = $input->getOption('interval');

        $logLevel = Resque_Worker::LOG_NORMAL;
        if ($input->getOption('verbose')) {
            $logLevel = Resque_Worker::LOG_VERBOSE;
        }
        if ($input->getOption('quiet')) {
            $logLevel = Resque_Worker::LOG_NONE;
        }

        Resque_Event::listen('beforePerform', array($this, 'beforePerform'));

        $worker = $this->createWorker($queues);
        $worker->logLevel = $logLevel;
        // $worker->setLogger(array($output, 'writeln'));
        $output->writeln('*** Starting worker '.$worker);
        $worker->work($interval);
    }

    public function createWorker($queues)
    {
        return new Resque_Worker($queues);
    }

    public function beforePerform(Resque_Job $job)
    {
        $job->instance->app = $this->app;
    }
}
