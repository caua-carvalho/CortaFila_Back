<?php

namespace App\Controllers;

use App\Repositories\ServiceRepository;
use App\Core\RequestContext;

class ServiceController
{
    private ServiceRepository $service;

    public function __construct()
    {
        $this->service = new ServiceRepository();
    }

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

        $auth = RequestContext::get('auth_user');
        $companyId = $auth['company_id'];

        $result  = $this->service->update($id, $companyId,  $data);

        echo json_encode($result);
    }

    public function create()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $auth = RequestContext::get('auth_user');
        $companyId = $auth['company_id'];

        $result = $this->service->create($body, $companyId);

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
