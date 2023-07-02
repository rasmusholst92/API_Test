<?php

namespace App\Controller;

use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use \Firebase\JWT\JWT;
use LoginService;


class LoginController
{
    private $responseFactory;
    private $service;

    private const JWT_ISSUER = 'mydomain.com';
    private const JWT_AUDIENCE = 'mydomain.com';
    private const JWT_EXPIRATION_TIME = 60 * 60; // 1 hour

    public function __construct(ResponseFactory $responseFactory, LoginService $service)
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

            if (!password_verify($data['password'], $user['password'])) {
                return $response->withStatus(401); // Unauthorized
            }

            $key = $_ENV['JWT_SECRET'];

            $payload = array(
                "iss" => self::JWT_ISSUER,
                "aud" => self::JWT_AUDIENCE,
                "iat" => time(),
                "exp" => time() + self::JWT_EXPIRATION_TIME,
                "sub" => $user['user_id'],
                "role" => $user['role']
            );

            $jwt = JWT::encode($payload, $key, 'HS256');

            $response->getBody()->write(json_encode([
                'status' => 200,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['user_id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ],
                'bearer' => $jwt
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // Internal error
        }
    }
}