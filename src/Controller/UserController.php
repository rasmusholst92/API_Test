<?php
namespace App\Controller;

use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use UserService;

class UserController
{
    private $responseFactory;
    private $service;

    public function __construct(ResponseFactory $responseFactory, UserService $service)
    {
        $this->responseFactory = $responseFactory;
        $this->service = $service;
    }

    public function getUsers(Request $request, Response $response, $args)
    {
        try {
            $users = $this->service->getUsers();
            $response->getBody()->write(json_encode($users));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function getUserById(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $user = $this->service->getUserById($id);
            if ($user === null) {
                return $response->withStatus(401);
            }
            $response->getBody()->write(json_encode($user));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function createUser(Request $request, Response $response, $args)
    {
        try {
            $data = $request->getParsedBody();
            $existingUsername = $this->service->findUserByUsername($data['username']);
            $existingEmail = $this->service->findUserByEmail($data['email']);
            if ($existingUsername) {
                $response->getBody()->write(json_encode(['status' => '409', 'message' => 'Username already exists.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(409); // Conflict
            }
            if ($existingEmail) {
                $response->getBody()->write(json_encode(['status' => '419', 'message' => 'Email already exists.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(419); // Conflict
            }
            $newUserId = $this->service->createUser($data);
            $response->getBody()->write(json_encode(['message' => "User successfully created"]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $errors = json_decode($e->getMessage(), true);
            $response->getBody()->write(json_encode(['errors' => $errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Invalid data
        }
    }

    public function deleteUser(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $this->service->deleteUser($id);
            $response->getBody()->write(json_encode(['message' => 'User deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => 'User not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404); // Not found
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // Internal error
        }
    }

    public function updateUser(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $data = $request->getParsedBody();

            $this->service->updateUser($id, $data);
            $response->getBody()->write(json_encode(['message' => "User successfully updated"]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $errors = json_decode($e->getMessage(), true);
            if (!is_array($errors)) {
                $errors = [$e->getMessage()];
            }

            $response->getBody()->write(json_encode(['errors' => $errors]));
            $status = $e instanceof \InvalidArgumentException ? 400 : 500;
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }

    public function getUserByUsername(Request $request, Response $response, $args)
    {
        try {
            $username = $args['username'];
            $user = $this->service->findUserByUsername($username);
            if ($user === null || $user === false) {
                $response->getBody()->write(json_encode(['error' => 'User not found']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404); // Not Found
            }
            $response->getBody()->write(json_encode($user));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }
}