<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class GetUser implements MiddlewareInterface
{
    public function __construct(private UserRepository $userRepository) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();
        $id = $route->getArgument('id');

        $user = $this->userRepository->find('id', $id);

        if ($user === false) {
            throw new HttpNotFoundException($request, "User Not Found");
        }

        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }
}
