<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Valitron\Validator;

class Signup
{
    public function __construct(
        private Twig $twig,
        private Validator $validator,
        private UserRepository $repository
    ) {
        $this->validator->setPrependLabels(false);
        $this->validator->mapFieldsRules([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', ['lengthMin', 6]],
            'password_confirmation' => ['required', ['equals', 'password']]
        ]);
    }

    public function new(Request $request, Response $response) : Response
    {
        $response = $this->twig->render($response, 'signup.html');

        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->validator = $this->validator->withData($data);

        if (!$this->validator->validate()) {
            return $this->twig->render($response, 'signup.html', [
                'errors' => $this->validator->errors(),
                'data' => $data
            ]);
        }

        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $api_key = bin2hex(random_bytes(16));

        $data['api_key'] ='';

        $data['api_key_hash'] = hash_hmac('sha256', $api_key, $_ENV['HASH_SECRET_KEY']);

        $this->repository->create($data);

        $response->getBody()->write("Your API key is: $api_key");

        return $response;
    }
}
