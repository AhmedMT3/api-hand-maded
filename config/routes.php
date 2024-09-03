<?php

declare(strict_types=1);

use App\Controllers\Home;
use App\Controllers\Login;
use App\Controllers\Product;
use App\Controllers\Signup;
use App\Middleware\ActivateSession;
use App\Middleware\GetProduct;
use App\Middleware\RequireAPIKey;
use App\Middleware\AddJsonResponseHeader;
use Slim\Routing\RouteCollectorProxy;

$app->group('', function (RouteCollectorProxy $group) {
    $group->get('/', Home::class);

    $group->get('/signup', [Signup::class, 'new']);

    $group->post('/signup', [Signup::class, 'create']);

    $group->get('/login', [Login::class, 'new']);

    $group->post('/login', [Login::class, 'create']);

    $group->get('/logout', [Login::class, 'destroy']);
})->add(ActivateSession::class);


$app->group('/api', function (RouteCollectorProxy $group) {

    $group->get('/products', [Product::class, 'index']);

    $group->post('/products', [Product::class, 'create']);

    $group->group('', function (RouteCollectorProxy $group) {

        $group->get('/products/{id:[0-9]+}', [Product::class, 'show']);
        $group->patch('/products/{id:[0-9]+}', [Product::class, 'update']);
        $group->delete('/products/{id:[0-9]+}', [Product::class, 'delete']);
    })->add(GetProduct::class);
})->add(RequireAPIKey::class)
    ->add(AddJsonResponseHeader::class);
