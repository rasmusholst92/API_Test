<?php

use Dotenv\Dotenv;

// Import various repositories, services, and controllers.
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
$corsFactory = require_once __DIR__ . '/../config/cors.php';
$corsFactory($app);

// Establish PDO database connection  
$pdoFactory = require_once __DIR__ . '/../config/database.php';
$pdo = $pdoFactory();

// Add error middleware  
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Create instances of the repositories and services  
$userRepository = new UserRepository($pdo);
$userService = new UserService($userRepository);

// Define API routes
require_once __DIR__ . '/../src/routes.php';
getRoutes($app, $responseFactory, $userService);

// Run the application  
$app->run($request);