<?php

class LoginRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findUserByUsername($username)
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE username = :username');
        $statement->bindParam(':username', $username);
        $statement->execute();
        return $statement->fetch();
    }

    // public function findUserByEmail($email)
    // {
    //     $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
    //     $statement->bindParam(':email', $email);
    //     $statement->execute();
    //     return $statement->fetch();
    // }
}