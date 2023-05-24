<?php

class CustomerRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllCustomers()
    {
        $statement = $this->pdo->query('SELECT * FROM customers');
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function createCustomer($data)
    {
        $statement = $this->pdo->prepare('INSERT INTO customers (first_name, last_name, street_name, house_no, zipcode)
            VALUES (:first_name, :last_name, :street_name, :house_no, :zipcode)');
        $statement->execute($data);
        $customerID = $this->pdo->lastInsertId();
        return $this->getCustomerById($customerID);
    }
}