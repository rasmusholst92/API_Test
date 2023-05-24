<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Repository/CustomerRepository.php';
require_once __DIR__ . '/Service/CustomerService.php';
require_once __DIR__ . '/Controller/CustomerController.php';

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteCollectorProxy;
use Tuupola\Middleware\CorsMiddleware;

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

require_once __DIR__ . '/config/database.php';

$customerRepository = new CustomerRepository($pdo);
$customerService = new CustomerService($customerRepository);
$customerController = new CustomerController($customerService);

$app->addErrorMiddleware(true, true, true);

$app->group('/api', function (RouteCollectorProxy $group) use ($customerController) {
    $group->get('/customers', [$customerController, 'getCustomers']);
    $group->get('/customers/{id}', [$customerController, 'getCustomerById']);
    $group->post('/customers', [$customerController, 'createCustomer']);
});

$app->run($request);