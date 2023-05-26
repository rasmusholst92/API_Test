<?php

class UserRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllUsers()
    {
        $statement = $this->pdo->query('SELECT * FROM users');
        return $statement->fetchAll();
    }

    public function findUserById($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE user_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch();
    }

    public function createUser($data)
    {
        $statement = $this->pdo->prepare('INSERT INTO users (username, email) 
        VALUES (:username, :email)');
        $statement->bindValue(':username', $data['username']);
        $statement->bindValue(':email', $data['email']);
        $statement->execute();
        return $this->pdo->lastInsertId();
    }

    public function deleteUser($id)
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE user_id = :id');
        $statement->bindParam(':id', $id);
        $statement->execute();
        $user = $statement->fetch();

        if (!$user) {
            throw new \InvalidArgumentException('User not found');
        }

        $deleteStatement = $this->pdo->prepare('DELETE FROM users WHERE user_id = :id');
        $deleteStatement->bindParam(':id', $id);
        $deleteStatement->execute();
    }

    public function updateUser($id, $data)
    {
        $statement = $this->pdo->prepare('UPDATE users SET username = :username, email = :email WHERE user_id = :id');
        $statement->execute([
            ':id' => $id,
            ':username' => $data['username'],
            ':email' => $data['email']
        ]);
    }

}