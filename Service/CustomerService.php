<?php

class CustomerService
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getAllCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    // Add the missing getCustomers method
    public function getCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }

    public function getCustomerById($id)
    {
        return $this->customerRepository->getCustomerById($id);
    }

    public function createCustomer($data)
    {
        return $this->customerRepository->createCustomer($data);
    }
}