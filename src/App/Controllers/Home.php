<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Home
{
    public function __construct(private Twig $twig) {}

    public function __invoke(Request $request, Response $response)
    {
        $response = $this->twig->render($response, 'home.html');

        return $response;
    }
}
