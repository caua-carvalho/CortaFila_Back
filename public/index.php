<?php

// CORS padrão e consistente
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept, Origin, User-Agent");
header("Access-Control-Max-Age: 86400"); // cache do preflight

// Se for preflight, responde e finaliza ANTES de rodar qualquer código
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204); // no content
    exit();
}

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

// Resposta padrão JSON
header('Content-Type: application/json; charset=utf-8');

$app->run();
