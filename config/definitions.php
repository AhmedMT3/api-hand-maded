<?php

use App\Database;
use Slim\Views\Twig;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    Database::class => function () {
        return new Database(
            host: $_ENV['DB_HOST'],
            name: $_ENV['DB_NAME'],
            user: $_ENV['DB_USERNAME'],
            password: $_ENV['DB_PASSWORD']
        );
    },
    Twig::class => function () {

        return  Twig::create(__DIR__ . '/../views', ['cache' => false]);
    }
];
