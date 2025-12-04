<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';

use Core\Router;

$router = new Router();

// Carregar rotas
require_once __DIR__ . '/../routes/api.php';

$router->dispatch();
