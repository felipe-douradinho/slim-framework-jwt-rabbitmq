<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;


$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = env('ENV', 'prod') ?? 'dev';

$containerBuilder = new ContainerBuilder();


// Import services
(require __DIR__ . '/../app/services.php')($containerBuilder);


// Initialize app with PHP-DI
$container = $containerBuilder->build();
AppFactory::setContainer($container);

$app = AppFactory::create();


########################################
(require __DIR__ . '/../bootstrap/settings.php')($container); // @todo Create an Service provider to bootstrap it


########################################
$_SERVER['app'] = &$app;

if (!function_exists('app'))
{
    function app()
    {
        return $_SERVER['app'];
    }
}

########################################
require __DIR__ . '/../bootstrap/database.php'; // @todo Create an Service provider to bootstrap it

########################################
(require __DIR__ . '/../app/Http/Kernel.php')($app); // @todo Create an Service provider to bootstrap it

########################################
// Setup Basic Auth
//(require __DIR__ . '/../app/Http/Middleware/auth.basic.php')($app);

########################################
// Setup JWT Auth
(require __DIR__ . '/../app/Http/Middleware/auth.jwt.php')($app);


########################################
// Register routes
(require __DIR__ . '/../app/routes.php')($app); // @todo Create an Service provider to bootstrap it


return $app;
