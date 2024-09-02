<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class RequireAPIKey implements MiddlewareInterface
{
    public function __construct(private ResponseFactory $responseFactory) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $params = $request->getQueryParams();

        //if (! array_key_exists('api-key', $params)) {
        if (! $request->hasHeader('x-api-key')) {
            $response  = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode('API Key Missing!'));

            return $response->withStatus(400); //Bad Request
        }

        // if ($params['api-key'] !== 'abc123') {
        if ($request->getHeaderLine('X-API-Key') !== 'abc123') {
            $response  = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode('Invalid API Key!'));

            return $response->withStatus(401); //Unauthorized
        }

        $response = $handler->handle($request);

        return $response;
    }
}
