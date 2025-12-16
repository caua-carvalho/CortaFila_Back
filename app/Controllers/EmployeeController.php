<?php

namespace App\Controllers;

use App\Services\EmployeeService;
use App\Core\RequestContext;
use App\Repositories\EmployeeRepository;

class EmployeeController
{
    private EmployeeService $service;
    private EmployeeRepository $repository;
    
    private EmployeeService $invite;

    public function __construct()
    {
        $this->service = new EmployeeService();
        $this->repository = new EmployeeRepository();
    }

    public function all()
    {
        $auth = RequestContext::get('auth_user');
        $companyId = $auth['company_id'];

        $result = $this->repository->all($companyId);

        echo json_encode($result);
    }

    public function create()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $auth = RequestContext::get('auth_user');
        $companyId = $auth['company_id'];

        $result = $this->service->createEmployee(
            payload: $body,
            companyId: $companyId
        );

        echo json_encode($result);
    }

    public function activateEmployee()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $password = $body['password'];
        $token = $body['token'];

        $result = $this->service->activateEmployee($token, $password);

        echo json_encode($result);
    }

    public function findEmployeeByToken($token)
    {
        $user = $this->service->findByToken($token['token']);

        echo json_encode($user);
    }
}
