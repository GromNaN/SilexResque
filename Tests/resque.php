<?php

require __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$console = new Symfony\Component\Console\Application();

$console->add(new Grom\SilexResque\Command\ResqueWorkerCommand($app));

$console->run();
