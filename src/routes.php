<?php
// src/routes.php

use Slim\Routing\RouteCollectorProxy;

function getRoutes($app, $responseFactory, $userservice)
{
    $app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userservice) {
        $userController = new UserController($responseFactory, $userservice);

        $group->get('/users', [$userController, 'getUsers']);
        $group->get('/users/{id}', [$userController, 'getUserById']);
        $group->post('/users', [$userController, 'createUser']);
        $group->delete('/users/{id}', [$userController, 'deleteUser']);
        $group->put('/users/{id}', [$userController, 'updateUser']);
    });
}