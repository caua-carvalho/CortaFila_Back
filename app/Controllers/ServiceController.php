<?php

namespace App\Controllers;

use App\Repositories\UserRepository;

class ServiceController
{
    private UserRepository $service;

    public function all()
    {
        $result = $this->service->all();
        echo json_encode($result);
    }


    public function findById($id)
    {
        $id = $id['id'];
        $result = $this->service->findById($id);
        echo json_encode($result);
    }

    public function update()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $id   = $body["id"];
        $data = $body["data"];

        $result  = $this->service->update($id, $data);

        echo json_encode($result);
    }

    public function create()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $result = $this->service->create($body);

        echo json_encode($result);
    }

    public function delete()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $id   = $body['id'];

        $result = $this->service->delete($id);

        echo json_encode($result);
    }
}
