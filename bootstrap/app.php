<?php

use Core\Router;

// Carregar env (se existir)
if (file_exists(__DIR__ . '/../app/config/env.php')) {
    require_once __DIR__ . '/../app/config/env.php';
}

// Carregar env do filesystem APENAS para DEV
// Em produção (Render), ENV vem de variável de sistema
if (function_exists('load_env') && file_exists(__DIR__ . '/../.env')) {
    load_env(__DIR__ . '/../.env');
}

// Registrar configs
require_once __DIR__ . '/../app/config/config.php';

// Instância do router
$router = new Router();

// Carregar rotas
require_once __DIR__ . '/../routes/api.php';

return new class($router) {
    public function __construct(private $router) {}

    public function run() {
        $this->router->dispatch();
    }
};
