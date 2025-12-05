<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class UserController
{
    private UserRepository $users;

    public function __construct()
    {
        $this->users = new UserRepository();
    }

    public function index()
    {
        $result = $this->users->all();
        echo json_encode($result);
    }

    public function findById($id)
    {
        $id = $id['id'];
        $result = $this->users->findById($id);
        echo json_encode($result);
    }

    public function store()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $result = $this->users->create($body);

        echo json_encode($result);
    }
}
