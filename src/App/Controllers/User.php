<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class User
{
    public function __construct(
        private UserRepository $userRepository,
        private Validator $validator
    ) {
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', ['lengthMin', 6]],
            'password_confirmation' => ['required', ['equals', 'password']]
        ]);
        // Custom validation rule for email if exist
        $this->validator->rule(function ($field, $value, $params, $fields) {
            return $this->userRepository->find('email', $value) === false;
        }, 'email')->message('{field} is already taken');
    }

    public function index(Request $request, Response $response): Response
    {
        $data = $this->userRepository->getAll();

        $response->getBody()->write(json_encode($data));

        return $response;
    }

    public function show(Request $request, Response $response): Response
    {
        //user was added to attribute by [GetUser] middleware
        $user = $request->getAttribute('user');
        $body = json_encode($user);

        $response->getBody()->write($body);

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->validator =  $this->validator->withData($data);

        if (! $this->validator->validate()) {
            $response->getBody()->write(
                json_encode($this->validator->errors())
            );
            return $response->withStatus(422); // Unprocessabl Entity.
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $api_key = bin2hex(random_bytes(16));

        $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);

        $data['api_key'] = Crypto::encrypt($api_key, $encryption_key);

        $data['api_key_hash'] = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);

        $id = $this->userRepository->create($data);

        $response->getBody()->write(json_encode([
            'message' => 'User Created Successfuly',
            'api_key' => $api_key,
            'id' => (int) $id
        ]));

        return $response->withStatus(201); // Created
    }

    public function update(Request $request, Response $response, string $id): Response
    {
        $data = $request->getParsedBody();

        $this->validator =  $this->validator->withData($data);

        if (! $this->validator->validate()) {
            $response->getBody()->write(
                json_encode($this->validator->errors())
            );
            return $response->withStatus(422); // Unprocessabl Entity.
        }
        // hash the given password        
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        // delete unwanted fields
        unset($data['password']);
        unset($data['password_confirmation']);

        $rows = $this->userRepository->update((int) $id, $data);

        $body = json_encode([
            'message' => "User $id Updated",
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }

    public function delete(Request $request, Response $response, string $id): Response
    {
        $rows = $this->userRepository->delete((int) $id);

        $body = json_encode([
            'message' => "User $id Deleted",
            'rows' => $rows
        ]);

        $response->getBody()->write($body);

        return $response;
    }
}
