<?php

use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Psr7\Factory\ResponseFactory;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/CustomerController.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$app = AppFactory::create();
$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$responseFactory = new ResponseFactory();

$pdo = new PDO(
    'mysql:host=' . $_ENV['DB_HOST'] . ';port=' . $_ENV['DB_PORT'] . ';dbname=' . $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
);

$app->addErrorMiddleware(true, true, true);

$app->group('/api', function (RouteCollectorProxy $group) use ($responseFactory, $pdo) {
    $customerController = new CustomerController($responseFactory, $pdo);

    $group->get('/customers', [$customerController, 'getCustomers']);
    $group->get('/customers/{id}', [$customerController, 'getCustomerById']);
});

$app->run($request);