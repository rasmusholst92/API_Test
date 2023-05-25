<?php
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CustomerController
{
    private $responseFactory;
    private $service;

    public function __construct(ResponseFactory $responseFactory, CustomerService $service)
    {
        $this->responseFactory = $responseFactory;
        $this->service = $service;
    }

    public function getCustomers(Request $request, Response $response, $args)
    {
        try {
            $customers = $this->service->getCustomers();
            $response->getBody()->write(json_encode($customers));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function getCustomerById(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $customer = $this->service->getCustomerById($id);
            if ($customer === null) {
                return $response->withStatus(401);
            }
            $response->getBody()->write(json_encode($customer));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write($e->getMessage());
            return $response->withStatus(500);
        }
    }

    public function createCustomer(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        if ($data === null || !is_array($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid data format']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Invalid data
        }
        $newCustomerId = $this->service->createCustomer($data);
        $response->getBody()->write(json_encode(['message' => "Customer successfully created"]));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function deleteCustomer(Request $request, Response $response, $args)
    {
        try {
            $id = $args['id'];
            $this->service->deleteCustomer($id);
            $response->getBody()->write(json_encode(['message' => 'Customer deleted successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\InvalidArgumentException $e) {
            $response->getBody()->write(json_encode(['error' => 'Customer not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404); // Not found
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500); // Internal error
        }
    }

    public function updateCustomer(Request $request, Response $response, $args)
    {
        $id = $args['id'];
        $data = $request->getParsedBody();

        if ($data === null || !is_array($data)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid data format']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->service->updateCustomer($id, $data);
            $response->getBody()->write(json_encode(['message' => "Customer successfully updated"]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withStatus(404); // Not found
        }
    }
}