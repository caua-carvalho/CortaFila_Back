<?php

namespace Core;

class Router
{
    private array $routes = [
        'GET'     => [],
        'POST'    => [],
        'PUT'     => [],
        'DELETE'  => []
    ];

    public function get(string $path, string $handler)
    {
        $this->addRoute('GET', $path, $handler);
    }

    public function post(string $path, string $handler, array $middlewares = [])
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }


    public function put(string $path, string $handler)
    {
        $this->addRoute('PUT', $path, $handler);
    }

    public function delete(string $path, string $handler)
    {
        $this->addRoute('DELETE', $path, $handler);
    }

    private function addRoute(string $method, string $path, string $handler, array $middlewares = [])
    {
        $this->routes[$method][] = [
            'path'        => $this->normalize($path),
            'regex'       => $this->pathToRegex($path),
            'handler'     => $handler,
            'middlewares' => $middlewares
        ];
    }

    private function normalize(string $path)
    {
        return '/' . trim($path, '/');
    }

    private function pathToRegex(string $path): string
    {
        $path = $this->normalize($path);

        // transforma /users/{id} em /users/(?P<id>[^/]+)
        $regex = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $path);

        return '#^' . $regex . '$#';
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        if (defined('BASE_PATH')) {
            $path = str_replace(search: BASE_PATH, replace: '', subject: $path);
        }

        // Normaliza path
        $path = $this->normalize($path);

        if (!isset($this->routes[$method])) {
            http_response_code(404);
            echo json_encode(['error' => 'Route not found']);
            exit;
        }

        foreach ($this->routes[$method] as $route) {

            if (preg_match($route['regex'], $path, $matches)) {

                // RUN MIDDLEWARES
                if (!empty($route['middlewares'])) {
                    foreach ($route['middlewares'] as $middlewareClass) {
                        $middleware = new $middlewareClass();
                        $middleware->handle();
                    }
                }

                // extrai parâmetros
                $params = array_filter(
                    $matches,
                    fn($key) => !is_int($key),
                    ARRAY_FILTER_USE_KEY
                );

                return $this->executeHandler($route['handler'], $params);
            }
        }


        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
        exit;
    }

    private function executeHandler(string $handler, array $params)
    {
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
            throw new \Exception("Método {$methodName} não encontrado em {$controllerClass}");
        }

        // passa params diretamente pro método
        return $controller->{$methodName}($params);
    }
}
