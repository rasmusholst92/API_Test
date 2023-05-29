<?php

class UserService
{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getUsers()
    {
        return $this->repository->getAllUsers();
    }

    public function getUserById($id)
    {
        return $this->repository->findUserById($id);
    }

    public function createUser($data)
    {
        return $this->repository->createUser($data);
    }

    public function deleteUser($id)
    {
        $user = $this->repository->findUserById($id);

        if (!$user) {
            throw new Exception('User not found');
        }

        $this->repository->deleteUser($id);
    }

    public function updateUser($id, $data)
    {
        $user = $this->repository->findUserById($id);
        if (!$user) {
            throw new Exception("User not found");
        }

        $this->repository->updateUser($id, $data);
    }

    public function findUserByUsername($username)
    {
        return $this->repository->findUserByUsername($username);
    }
}