<?php

namespace Grom\SilexResque\Tests\Job;

use Silex\Application;
use Grom\SilexResque\BaseJob;

class Job extends BaseJob
{
    public function execute(array $args, Application $app)
    {
        sleep(10);
        fwrite(STDOUT, 'Hello!'.PHP_EOL);
    }
}
