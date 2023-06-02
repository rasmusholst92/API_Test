<?php

use App\Controller\LoginController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

// TO DO
// Authentication på /users/{id} hvis brugerens id er det givende id der er logget ind.

function getRoutes($app, $responseFactory, $userservice)
{
    $app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userservice) {
        $userController = new UserController($responseFactory, $userservice);
        $loginController = new LoginController($responseFactory, $userservice);

        $group->post('/login', [$loginController, 'loginUser']);

        $group->group('', function (RouteCollectorProxy $group) use ($userController) {
            $group->get('/users', [$userController, 'getUsers']);
            $group->get('/users/{id}', [$userController, 'getUserById']);
            $group->post('/users', [$userController, 'createUser']);
            $group->delete('/users/{id}', [$userController, 'deleteUser']);
            $group->put('/users/{id}', [$userController, 'updateUser']);
        })->add(new AuthMiddleware($_ENV['JWT_SECRET']));
    });
}