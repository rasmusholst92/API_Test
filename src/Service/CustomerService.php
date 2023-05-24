<?php

class CustomerService
{
    private $repository;

    public function __construct(CustomerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getCustomers()
    {
        return $this->repository->getAllCustomers();
    }

    public function getCustomerById($id)
    {
        return $this->repository->findCustomerById($id);
    }

    public function createCustomer($data)
    {
        return $this->repository->createCustomer($data);
    }

    public function deleteCustomer($id)
    {
        $customer = $this->repository->findCustomerById($id);

        if (!$customer) {
            throw new Exception('Customer not found');
        }

        $this->repository->deleteCustomer($id);
    }

    public function updateCustomer($id, $data)
    {
        $customer = $this->repository->findCustomerById($id);
        if (!$customer) {
            throw new Exception("Customer not found");
        }

        $this->repository->updateCustomer($id, $data);
    }
}