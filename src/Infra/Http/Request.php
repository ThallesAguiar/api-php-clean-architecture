<?php

namespace App\Infra\Http;

class Request
{
    private array $data;
    private string $method;
    private string $uri;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->data = $this->getRequestData();
    }

    private function getRequestData(): array
    {
        $data = [];

        // Dados do POST
        if ($this->method === 'POST') {
            $data = $_POST;
        }

        // Dados do JSON
        $input = file_get_contents('php://input');
        if (!empty($input)) {
            $jsonData = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = array_merge($data, $jsonData);
            }
        }

        // Dados do GET
        if ($this->method === 'GET') {
            $data = $_GET;
        }

        return $data;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    public function all(): array
    {
        return $this->data;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }
}