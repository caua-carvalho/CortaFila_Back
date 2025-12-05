<?php
ob_start(); // inicia buffer de saída

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/env.php';

load_env(__DIR__ . '/../.env');

use Core\Router;

$router = new Router();

// Carregar rotas
require_once __DIR__ . '/../routes/api.php';

header('Content-Type: application/json; charset=utf-8');
$router->dispatch();
ob_end_flush();
