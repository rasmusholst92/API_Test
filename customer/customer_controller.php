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
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id =  ' . $id);
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

    public function createCustomer(Request $request, Response $response): Response
    {
        $data = json_decode($request->getBody(), true);

        $statement = $this->pdo->prepare('INSERT INTO customers (first_name, last_name, street_name, house_no, zipcode)
            VALUES (:first_name, :last_name, :street_name, :house_no, :zipcode)');
        $statement->execute($data);

        $customerID = $this->pdo->lastInsertId();
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id = ' . $customerID);
        $statement->execute();
        $customer = $statement->fetch();

        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        if ($customer) {
            $response = $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
            $response->getBody()->write(json_encode($customer));
        } else {
            $response = $response
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode(['error' => 'Failed to retrieve data']));
        }

        return $response;
    }
}