# SilexResque

This package offers improved integration of the php-resque package into your Silex project. It comes with a console command to maintain a worker, and an abstract job class that makes the Silex application available to each job. 

## Installation

```sh
php composer.phar require grom/silex-resque:dev-master
```

If you already have a console set up, just add the `Grom\SilexResque\Command\ResqueWorkerCommand($app)` command to it. If you don't, here's an easy way to make one:

```sh
php composer.phar require knplabs/console-service-provider:dev-master
```

Then create a file called something like `bin/console`, make it executable then fill it with something like:

```php
#!/usr/bin/env php
<?php
$loader = require __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application;

/* Your bootstrap script, shared with the web app
 * Should "return $app" and configure all services.
 * Or, you know, configure your services here instead.
 */
$app = require __DIR__.'/src/bootstrap.php'; 

// Register console provider
$app->register(new Knp\Provider\ConsoleServiceProvider(), array(
    'console.name'              => 'My Project Console',
    'console.version'           => '1.0.0-alpha,
    'console.project_directory' => __DIR__,
));

// Add the Resque worker command
$app['console']->add(new \Grom\SilexResque\Command\ResqueWorkerCommand($app));

// ...add any other commands you want

$app['console']->run();
```

Then to start a worker, run `bin/console resque:worker default` (where `default` is the name of a queue.)


## Creating a job

Create a job class that extends `Grom\SilexResque\BaseJob` and fill in the required `execute` function:

```php
<?php
namespace MyVendor\MyProject\Job;

use Grom\SilexResque\BaseJob;
use Silex\Application;

class TestJob extends BaseJob
{
    protected function execute(array $args, Application $app)
    {
        // do stuff here, e.g.
        print_r($args);
    }
}
```

Now to queue a job from your Silex application:

```php
$app->get('/queuetest', function () {
    $token = Resque::enqueue('default', 'MyVendor\MyProject\Job\TestJob', array(
        'arg1' => 1,
        'stuff' => 'things',
    ), true);
    
    return print_r($token, true);
});

```

The token can be used to get information about a job later:

```php
$status = new Resque_Job_Status($token);
echo $status->get(); // Outputs the status
```

## Monitoring jobs

Because PHP-Resque is compatible with the original Ruby edition, you can use its webapp to manage and monitor your queue, retry failed jobs etc. Install it with `gem install resque`, then run it with `resque-web -p 5678`. When you're finished, you can kill its server with `resque-web --kill`.
