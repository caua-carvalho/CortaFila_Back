<?php

$router->get('/', 'HomeController@index');

$router->get('/clients', 'ClientController@index');
$router->post('/clients', 'ClientController@store');
$router->get('/professional/show', 'ProfessionalController@show');   // exemplo

$router->get('/users', 'UserController@index');
$router->get('/users/{id}', 'UserController@findById');
$router->post('/users/login', 'UserController@login');


$router->post('/company/create', 'CompanyController@store');