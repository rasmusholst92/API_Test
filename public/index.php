<?php

use Slim\Routing\RouteCollectorProxy;
use Dotenv\Dotenv;

// Importere diverse repositories, services og controllers.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Controller/UserController.php';
require_once __DIR__ . '/../src/Service/UserService.php';
require_once __DIR__ . '/../src/Repository/UserRepository.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Create Slim app instance
$appFactory = require_once __DIR__ . '/../config/app.php';
$app = $appFactory();

// Create server request instance
$requestFactory = require_once __DIR__ . '/../config/request.php';
$request = $requestFactory();

// Set up response factory
$responseFactory = $app->getResponseFactory();

// Configure CORS middleware
$corsFactory = require_once __DIR__ . '/..cu/config/cors.php';
$corsFactory($app);

// Establish PDO database connection
$pdoFactory = require_once __DIR__ . '/../config/database.php';
$pdo = $pdoFactory();

// Add error middleware
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Create instances of the repositories and servicies
$userRepository = new userRepository($pdo);
$userService = new userService($userRepository);

// Define API routes
$app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $userService) {
    $userController = new userController($responseFactory, $userService);

    $group->get('/users', [$userController, 'getUsers']);
    $group->get('/users/{id}', [$userController, 'getUserById']);
    $group->post('/users', [$userController, 'createUser']);
    $group->delete('/users/{id}', [$userController, 'deleteUser']);
    $group->put('/users/{id}', [$userController, 'updateUser']);
});

// Run the application
$app->run($request);