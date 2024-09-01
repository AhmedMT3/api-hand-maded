<?php

declare(strict_types=1);

namespace App;

use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use DI\ContainerBuilder;
use Slim\Handlers\Strategies\RequestResponseArgs;

define('APP_ROOT', dirname(__DIR__));


require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType('application/json');

$app->add(new Middleware\AddJsonResponseHeader);

$app->get('/api/products', function (Request $request, Response $response) {

    $repository = $this->get(Repositories\ProductRepository::class);
    $data = $repository->getAll();

    $body = json_encode($data);

    $response->getBody()->write($body);

    return $response;
});

$app->get('/api/products/{id:[0-9]+}', function (Request $request, Response $response, string $id) {

    $product = $request->getAttribute('product');

    $body = json_encode($product);
    $response->getBody()->write($body);

    return $response;
})->add(Middleware\GetProduct::class);

$app->run();
