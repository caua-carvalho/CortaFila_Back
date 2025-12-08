<?php

namespace App\Controllers;

use App\Services\EmployeeService;
use App\Core\RequestContext;

class EmployeeController
{
    private EmployeeService $service;

    public function __construct()
    {
        $this->service = new EmployeeService();
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
}
