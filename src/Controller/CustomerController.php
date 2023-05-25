<?php
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Validation\CustomerValidation;

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
        try {
            $data = $request->getParsedBody();
            CustomerValidation::validate($data);
            $newCustomerId = $this->service->createCustomer($data);
            $response->getBody()->write(json_encode(['message' => "Customer successfully created"]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $errors = json_decode($e->getMessage(), true);
            $response->getBody()->write(json_encode(['errors' => $errors]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400); // Invalid data
        }
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
        try {
            $id = $args['id'];
            $data = $request->getParsedBody();

            // Validate data using our validation class
            CustomerValidation::validate($data);

            $this->service->updateCustomer($id, $data);
            $response->getBody()->write(json_encode(['message' => "Customer successfully updated"]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // If an exception occurs, decode the error messages
            $errors = json_decode($e->getMessage(), true);
            // If the messages aren't an array (i.e., it's a different kind of exception), put the message in an array
            if (!is_array($errors)) {
                $errors = [$e->getMessage()];
            }

            // Return all error messages
            $response->getBody()->write(json_encode(['errors' => $errors]));

            // Determine the status code based on the type of exception
            $status = $e instanceof \InvalidArgumentException ? 400 : 500;
            return $response->withHeader('Content-Type', 'application/json')->withStatus($status);
        }
    }
}