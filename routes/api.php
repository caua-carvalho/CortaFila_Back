<?php

$router->get('/', 'HomeController@index');

$router->get('/clients', 'ClientController@index');
$router->post('/clients', 'ClientController@store');
$router->get('/professional/show', 'ProfessionalController@show');   // exemplo
