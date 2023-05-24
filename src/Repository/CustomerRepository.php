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
        return $statement->fetchAll();
    }

    public function findCustomerById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch();
    }

    public function createCustomer($data)
    {
        $statement = $this->pdo->prepare('INSERT INTO customers (first_name, last_name, address, zipcode) 
        VALUES (:first_name, :last_name, :address, :zipcode)');
        $statement->bindValue(':first_name', $data['first_name']);
        $statement->bindValue(':last_name', $data['last_name']);
        $statement->bindValue(':address', $data['address']);
        $statement->bindValue(':zipcode', $data['zipcode']);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }

    public function deleteCustomer($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM customers WHERE customer_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        $customer = $statement->fetch();

        if (!$customer) {
            throw new \InvalidArgumentException('Customer not found');
        }

        $deleteStatement = $this->pdo->prepare('DELETE FROM customers WHERE customer_id = :id');
        $deleteStatement->bindParam(':id', $id);
        $deleteStatement->execute();
    }

    public function updateCustomer($id, $data)
    {
        $statement = $this->pdo->prepare('UPDATE customers SET first_name = :first_name, last_name = :last_name, address = :address, zipcode = :zipcode WHERE customer_id = :id');
        $statement->execute([
            ':id' => $id,
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':address' => $data['address'],
            ':zipcode' => $data['zipcode'],
        ]);
    }

}