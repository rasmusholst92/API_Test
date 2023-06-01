<?php
namespace App\Middleware;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response;

class AuthMiddleware
{
    private $jwtKey;

    public function __construct(string $jwtKey)
    {
        $this->jwtKey = $jwtKey;
    }

    public function __invoke($request, $handler)
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader) {
            $response = new Response();
            $response->getBody()->write(json_encode(['message' => 'Access denied!']));
            return $response->withStatus(401);
        }

        $jwt = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = (array) JWT::decode($jwt, new Key($this->jwtKey, 'HS256'));

            // Check if the user is an admin
            if (!isset($decoded['role']) || $decoded['role'] !== 'admin') {
                $response = new Response();
                $response->getBody()->write(json_encode(['message' => 'Access denied!']));
                return $response->withStatus(403);
            }

            $request = $request->withAttribute('jwt', $decoded);

            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['message' => 'Invalid JWT']));
            return $response->withStatus(401);
        }
    }
}