<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Factory\ResponseFactory;

class CustomerController
{
    private $responseFactory;
    private $pdo;

    public function __construct(ResponseFactory $responseFactory, PDO $pdo)
    {
        $this->responseFactory = $responseFactory;
        $this->pdo = $pdo;
    }

    public function getCustomers(Request $request, Response $response, $args)
    {
        $statement = $this->pdo->query('SELECT * FROM customers');
        $customers = $statement->fetchAll();
        $response->getBody()->write(json_encode($customers));

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getCustomerById(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        $customer = $statement->fetch();

        if (!$customer) {
            $response = $response->withStatus(404);
            $response->getBody()->write(json_encode(['error' => 'Customer not found']));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $response->getBody()->write(json_encode($customer));
        return $response->withHeader('Content-Type', 'application/json');
    }
}