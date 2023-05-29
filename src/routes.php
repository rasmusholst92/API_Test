<?php
// src/routes.php

use Slim\Routing\RouteCollectorProxy;

function getRoutes($app, $responseFactory, $userservice)
{
    $app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userservice) {
        $userController = new UserController($responseFactory, $userservice);
        $authMiddleware = new AuthMiddleware($_ENV['JWT_SECRET']);

        $group->post('/login', [$userController, 'loginUser']);

        $group->get('/users', [$userController, 'getUsers'])->add($authMiddleware);
        $group->get('/users/{id}', [$userController, 'getUserById'])->add($authMiddleware);
        $group->post('/users', [$userController, 'createUser'])->add($authMiddleware);
        $group->delete('/users/{id}', [$userController, 'deleteUser'])->add($authMiddleware);
        $group->put('/users/{id}', [$userController, 'updateUser'])->add($authMiddleware);
    });

}