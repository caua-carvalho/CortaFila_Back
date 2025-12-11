<?php

namespace App\Controllers;

use App\Services\CompanyService;

class CompanyController
{
    private CompanyService $service;

    public function __construct()
    {
        $this->service = new CompanyService();
    }

    public function store()
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $result = $this->service->createCompanyWithAdmin($body);

        echo json_encode($result);
    }
}
