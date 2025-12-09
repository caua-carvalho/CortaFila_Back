<?php
use App\Middlewares\AuthMiddleware;

$router->get('/', 'HomeController@index');

$router->get('/clients', 'ClientController@index');
$router->post('/clients', 'ClientController@store');
$router->get('/professional/show', 'ProfessionalController@show');   // exemplo

// USER
$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@findById');
$router->post('/users/login', 'UserController@login');
$router->post('/users/validate', 'UserController@validate');

// COMPANY
$router->post('/company/create', 'CompanyController@store');

// EMPLOYEE
$router->post('/employee/create', 'EmployeeController@create', [
    AuthMiddleware::class
]);