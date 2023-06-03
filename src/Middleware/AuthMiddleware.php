<?php
namespace App\Middleware;

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;

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

            // Get the ID from the route
            $routeContext = RouteContext::fromRequest($request);
            $routeArguments = $routeContext->getRoute()->getArguments();
            $requestedId = $routeArguments['id'] ?? null;

            // Check if the user is an admin or if the requested user matches the user in the JWT
            if ((!isset($decoded['role']) || $decoded['role'] !== 'admin') && (int) (!isset($decoded['sub']) || (int) $decoded['sub'] !== (int) $requestedId)) {
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