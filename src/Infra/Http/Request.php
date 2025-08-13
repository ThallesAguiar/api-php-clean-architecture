<?php

namespace App\Infra\Http;

use GuzzleHttp\Psr7\ServerRequest;

class Request extends ServerRequest
{
    public function __construct()
    {
        parent::__construct(
            $_SERVER['REQUEST_METHOD'] ?? 'GET',
            $_SERVER['REQUEST_URI'] ?? '/',
            getallheaders(),
            file_get_contents('php://input'),
            '1.1',
            $_SERVER
        );
    }

    public function getData(): array
    {
        if ($this->getMethod() === 'GET') {
            return $this->getQueryParams();
        }

        return $this->getParsedBody() ?? [];
    }
}
