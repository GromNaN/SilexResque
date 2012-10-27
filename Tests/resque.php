<?php

require __DIR__.'/../vendor/autoload.php';

$console = new Symfony\Component\Console\Application();

$console->add(new Grom\SilexResque\Command\ResqueWorkerCommand());

$console->run();
