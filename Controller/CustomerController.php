<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

class CustomerController
{
    private $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    public function getCustomers(Request $request, Response $response)
    {
        $customers = $this->customerService->getAllCustomers();
        $response->getBody()->write(json_encode($customers));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getCustomerById(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $customer = $this->customerService->getCustomerById($id);
        if (!$customer) {
            $response->getBody()->write(json_encode(['error' => 'Customer not found']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode($customer));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createCustomer(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $customer = $this->customerService->createCustomer($data);
        $response->getBody()->write(json_encode($customer));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
    }
}