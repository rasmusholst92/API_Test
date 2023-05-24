<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/Repository/CustomerRepository.php';
require_once __DIR__ . '/Service/CustomerService.php';
require_once __DIR__ . '/Controller/CustomerController.php';

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteCollectorProxy;
use Tuupola\Middleware\CorsMiddleware;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
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

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

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