<?php

class LoginService
{
    private $repository;

    public function __construct(LoginRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findUserByUsername($username)
    {
        return $this->repository->findUserByUsername($username);
    }

    // public function findUserByEmail($email)
    // {
    //     return $this->repository->findUserByEmail($email);
    // }
}