<?php

namespace App\Controller;

use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;
use UserService;


class LoginController
{
    private $responseFactory;
    private $service;

    public function __construct(ResponseFactory $responseFactory, UserService $service)
    {
        $this->responseFactory = $responseFactory;
        $this->service = $service;
    }

    public function loginUser(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getParsedBody();
            $user = $this->service->findUserByUsername($data['username']);

            if (!$user) {
                return $response->withStatus(401); // Unauthorized
            }

            if ($data['password'] !== $user['password']) {
                return $response->withStatus(401); // Unauthorized
            }

            $key = $_ENV['JWT_SECRET'];

            $payload = array(
                "iss" => "yourdomain.com",
                "aud" => "yourdomain.com",
                "iat" => time(),
                "exp" => time() + (60 * 60),
                "sub" => $user['user_id'],
                "role" => $user['role']
            );

            $jwt = JWT::encode($payload, $key, 'HS256');

            $response->getBody()->write(json_encode(['status' => 200, 'message' => 'Login successful', 'bearer' => $jwt]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // Internal error
        }
    }
}