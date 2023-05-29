<?php

use \Firebase\JWT\JWT;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

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
            $response->getBody()->write(json_encode(['message' => 'Missing Authorization header']));
            return $response->withStatus(401);
        }

        $jwt = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = (array) JWT::decode($jwt, $this->jwtKey, ['HS256']);

            $request = $request->withAttribute('jwt', (array) $decoded);

            return $handler->handle($request);
        } catch (\Exception $e) {
            $response = new Response();
            $response->getBody()->write(json_encode(['message' => 'Invalid JWT']));
            return $response->withStatus(401);
        }
    }
}