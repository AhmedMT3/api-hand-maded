<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;

class GetProduct implements MiddlewareInterface
{
    public function __construct(private ProductRepository $repository) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        $context = RouteContext::fromRequest($request);
        $route = $context->getRoute();
        $id = $route->getArgument('id');

        $product = $this->repository->getById((int) $id);

        if ($product === false) {
            throw new HttpNotFoundException($request, "Product not found");
        }

        $request = $request->withAttribute('product', $product);

        return $handler->handle($request);
    }
}
