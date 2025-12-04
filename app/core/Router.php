<?php

namespace Core;

class Router
{
    private array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => []
    ];

    public function get(string $path, string $handler)
    {
        $this->routes['GET'][$this->normalize($path)] = $handler;
    }

    public function post(string $path, string $handler)
    {
        $this->routes['POST'][$this->normalize($path)] = $handler;
    }

    public function put(string $path, string $handler)
    {
        $this->routes['PUT'][$this->normalize($path)] = $handler;
    }

    public function delete(string $path, string $handler)
    {
        $this->routes['DELETE'][$this->normalize($path)] = $handler;
    }

    private function normalize(string $path)
    {
        return '/' . trim($path, '/');
    }

    public function dispatch()
    {   
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $baseDir = '/CortaFila_Back/public';
        // Remove o caminho base definido
        if (defined('BASE_PATH')) {
            $path = str_replace(BASE_PATH, '', $path);
        }

        // Normaliza o path final
        $path = $this->normalize($path);


        if (!isset($this->routes[$method][$path])) {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
            exit;
        }

        $handler = $this->routes[$method][$path];

        // Formato esperado: "Controller@method"
        if (!str_contains($handler, '@')) {
            throw new \Exception("Handler inválido: use Controller@method");
        }

        [$controllerName, $methodName] = explode('@', $handler);

        $controllerClass = "\\App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            throw new \Exception("Controller {$controllerClass} não encontrado.");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            throw new \Exception("Método {$methodName} não encontrado no controller {$controllerClass}");
        }

        return $controller->{$methodName}();
    }
}
