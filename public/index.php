<?php

declare(strict_types=1);

namespace App;

use App\Controllers\Product;
use App\Middleware\AddJsonResponseHeader;
use App\Middleware\GetProduct;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Routing\RouteCollectorProxy;

define('APP_ROOT', dirname(__DIR__));


require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

$app->add(new AddJsonResponseHeader);

$app->group('/api', function (RouteCollectorProxy $group) {

    $group->get('/products', [Product::class, 'index']);

    $group->post('/products', [Product::class, 'create']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/products/{id:[0-9]+}', [Product::class, 'show']);
        $group->patch('/products/{id:[0-9]+}', [Product::class, 'update']);
        $group->delete('/products/{id:[0-9]+}', [Product::class, 'delete']);
    })->add(GetProduct::class);
});


$app->run();
