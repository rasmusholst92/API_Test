<?php

use Slim\Routing\RouteCollectorProxy;

function getRoutes($app, $responseFactory, $userservice)
{
    $app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userservice) {
        $userController = new UserController($responseFactory, $userservice);

        $group->post('/login', [$userController, 'loginUser']);

        $group->group('', function (RouteCollectorProxy $group) use ($userController) {
            $group->get('/users', [$userController, 'getUsers']);
            $group->get('/users/{id}', [$userController, 'getUserById']);
            $group->post('/users', [$userController, 'createUser']);
            $group->delete('/users/{id}', [$userController, 'deleteUser']);
            $group->put('/users/{id}', [$userController, 'updateUser']);
        })->add(new AuthMiddleware($_ENV['JWT_SECRET']));
    });
}