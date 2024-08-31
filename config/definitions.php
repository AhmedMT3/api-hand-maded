<?php

use App\Database;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

return [
    Database::class => new Database(
        host: $_ENV['DB_HOST'],
        name: $_ENV['DB_NAME'],
        user: $_ENV['DB_USERNAME'],
        password: $_ENV['DB_PASSWORD']
    )
];
