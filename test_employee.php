<?php

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Services\EmployeeService;

$service = new EmployeeService();

$result = $service->createEmployee([
    "name" => "Gustavo Alves",
    "email" => "gustavoxzbusiness@gmail.com",
    "phone" => "1298261420888"
], 1);

print_r($result);
