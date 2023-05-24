<?php

use Slim\Routing\RouteCollectorProxy;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Controller/CustomerController.php';
require_once __DIR__ . '/../src/Service/CustomerService.php';
require_once __DIR__ . '/../src/Repository/CustomerRepository.php';

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

// Create instances of the repositories and servicies
$customerRepository = new CustomerRepository($pdo);
$customerService = new CustomerService($customerRepository);

// Define API routes
$app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $customerService) {
    $customerController = new CustomerController($responseFactory, $customerService);

    $group->get('/customers', [$customerController, 'getCustomers']);
    $group->get('/customers/{id}', [$customerController, 'getCustomerById']);
    $group->post('/customers', [$customerController, 'createCustomer']);
    $group->delete('/customers/{id}', [$customerController, 'deleteCustomer']);
});

// Run the application
$app->run($request);