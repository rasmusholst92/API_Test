<?php

use App\Controller\LoginController;
use App\Controller\UserController;
use App\Middleware\AuthMiddleware;
use Slim\Routing\RouteCollectorProxy;

// TO DO: LoginController skal bruge ny LoginService & LoginRepository i stedet.

function getRoutes($app, $responseFactory, $userservice, $loginservice)
{
    $app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userservice, $loginservice) {
        $userController = new UserController($responseFactory, $userservice);
        $loginController = new LoginController($responseFactory, $loginservice);

        // Login Endpoints
        $group->post('/login', [$loginController, 'loginUser']);

        // Users Endpoints
        $group->post('/users/create', [$userController, 'createUser']);
        $group->get('/users/username/{username}', [$userController, 'getUserByUsername']);
        $group->group('', function (RouteCollectorProxy $group) use ($userController) {
            $group->get('/users', [$userController, 'getUsers']);
            $group->get('/users/{id}', [$userController, 'getUserById']);
            $group->delete('/users/{id}', [$userController, 'deleteUser']);
            $group->put('/users/{id}', [$userController, 'updateUser']);
        })->add(new AuthMiddleware($_ENV['JWT_SECRET']));
    });
}