<?php

declare(strict_types=1);

namespace App\Controllers;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Profile
{
    public function __construct(
        private Twig $twig
    ) {}

    public function show(Request $request, Response $response): Response
    {
        $user = $_SESSION['user'];
        //Decrypt the api_key
        $encryption_key = Key::loadFromAsciiSafeString($_ENV['ENCRYPTION_KEY']);
        $api_key = Crypto::decrypt($user['api_key'], $encryption_key);

        $response = $this->twig->render(
            $response,
            'profile.html',
            [
                'session' => $_SESSION,
                'api_key' => $api_key
            ]
        );

        return $response;
    }
}
