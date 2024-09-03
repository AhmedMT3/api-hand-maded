<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Repositories\UserRepository;
use Slim\Views\Twig;

class Login
{
    public function __construct(
        private Twig $twig,
        private UserRepository $repository
    ) {}

    public function new(Request $request, Response $response): Response
    {
        $response = $this->twig->render($response, 'login.html');

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $user = $this->repository->find('email', $data['email']);

        if ($user && password_verify($data['password'], $user['password_hash'])) {

            $_SESSION['user_name'] = $user['name'];

            return $response->withHeader('Location', '/')
                ->withStatus(302);
        }

        $response = $this->twig->render($response, 'login.html', [
            'data' => $data,
            'error' => 'Invalid email or password'
        ]);

        return $response;
    }

    public function destroy(Request $request, Response $response): Response
    {
        session_destroy();
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
