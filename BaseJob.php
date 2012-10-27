<?php

namespace Grom\SilexResque;

use Silex\Application;

abstract BaseJob
{
    /**
     * @var Silex\Application Context application
     */
    public $app;

    /**
     * @var Resque_Job
     */
    public $job;

    /**
     * @var array Arguments supplied to the job
     */
    public $args;

    /**
     * @var string Name of the queue.
     */
    public $queue;

    /**
     * Method called by Resque.
     */
    final public function perform()
    {
        $this->execute($this->args, $this->app);
    }

    /**
     * Do the job.
     *
     * @param array $args Arguments supplied to the job.
     * @param Application Silex application
     * @return void The return value is not used.
     */
    abstract protected function execute(array $args, Application $app);
}