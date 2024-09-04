<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class RequireLogin implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepository $repository
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (isset($_SESSION['user'])) {

            $user = $this->repository->find('id', $_SESSION['user']['id']);

            if ($user) {
                $request = $request->withAttribute('user', $user);
                return $handler->handle($request);
            }
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write("Unauthorized");

        return $response->withStatus(401);
    }
}
