<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\ProductRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class Product
{
    public function __construct(
        private ProductRepository $repository,
        private Validator $validator
    ) {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'size' => ['required', 'integer', ['min', 1]]
        ]);
    }

    public function index(Request $request, Response $response): Response
    {

        $data = $this->repository->getAll();

        $body = json_encode($data);

        $response->getBody()->write($body);

        return $response;
    }

    public function show(Request $request, Response $response): Response
    {
        $product = $request->getAttribute('product');

        $body = json_encode($product);

        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);
        if (! $this->validator->validate()) {
            $response->getBody()->write(
                json_encode($this->validator->errors())
            );
            return $response->withStatus(422); // Unprocessable Entity
        }

        $id = $this->repository->create($data);

        $body = json_encode([
            "message" => "Product Created",
            "id" => (int) $id
        ]);

        $response->getBody()->write($body);

        return $response->withStatus(201);
    }
    public function update(Request $request, Response $response, string $id): Response
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);
        if (! $this->validator->validate()) {
            $response->getBody()->write(
                json_encode($this->validator->errors())
            );
            return $response->withStatus(422); // Unprocessable Entity
        }

        $rows = $this->repository->update((int) $id, $data);

        $body = json_encode([
            "message" => "Product $id Updated",
            "rows" => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->repository->delete($id);

        $body = json_encode([
            "message" => "Product $id Deleted",
            "rows" => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
