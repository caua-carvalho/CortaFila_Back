<?php

namespace Core\Http;

class Request
{
    private array $headers;
    private array $body;
    private array $query;
    private string $method;
    private string $path;
    private array $server;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $this->headers = $this->parseHeaders();
        $this->query   = $_GET ?? [];

        $raw = file_get_contents('php://input');
        $this->body = json_decode($raw, true) ?? [];
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function header(string $key): ?string
    {
        return $this->headers[strtolower($key)] ?? null;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function json(): array
    {
        return $this->body;
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }
}
