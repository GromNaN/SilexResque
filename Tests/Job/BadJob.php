<?php

namespace Grom\SilexResque\Tests\Job;

use Silex\Application;
use Grom\SilexResque\BaseJob;

class BadJob extends BaseJob
{
    public function execute(array $args, Application $app)
    {
        throw new \Exception('Unable to run this job!');
    }
}
