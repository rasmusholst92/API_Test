<?php

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteCollectorProxy;
use Tuupola\Middleware\CorsMiddleware;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Customer/CustomerController.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$app = AppFactory::create();
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$responseFactory = $app->getResponseFactory();

// Add the CORS middleware
$app->add(new CorsMiddleware([
    'origin' => ['http://localhost:3000'],
    'methods' => ['GET', 'POST', 'PUT', 'DELETE'],
    'headers.allow' => ['Content-Type', 'Authorization'],
    'headers.expose' => [],
    'credentials' => true,
    'cache' => 0,
]));

// Require the database configuration
$pdoFactory = require_once __DIR__ . '/../config/database.php';
$pdo = $pdoFactory();

$app->addErrorMiddleware(true, true, true);

$app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $pdo) {
    $customerController = new CustomerController($responseFactory, $pdo);

    $group->get('/customers', [$customerController, 'getCustomers']);
    $group->get('/customers/{id}', [$customerController, 'getCustomerById']);
    $group->post('/customers', [$customerController, 'createCustomer']);
    $group->delete('/customers/{id}', [$customerController, 'deleteCustomer']);
});

$app->run($request);