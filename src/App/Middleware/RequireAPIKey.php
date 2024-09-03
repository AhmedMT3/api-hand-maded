<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

class RequireAPIKey implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private UserRepository $repository
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if (! $request->hasHeader('x-api-key')) {
            $response  = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode('API Key Missing!'));

            return $response->withStatus(400); //Bad Request
        }

        $api_key = $request->getHeaderLine('X-API-Key');
        
        $api_key_hash = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);

        $user_key = $this->repository->find('api_key_hash', $api_key_hash);

        if ($user_key === false) {

            $response  = $this->responseFactory->createResponse();
            $response->getBody()->write(json_encode('Invalid API Key!'));

            return $response->withStatus(401); //Unauthorized
        }

        $response = $handler->handle($request);

        return $response;
    }
}
