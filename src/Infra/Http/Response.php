<?php

namespace App\Infra\Http;

use App\Shared\Response\SituacaoEnum;

class Response
{
    public static function json(
        ?array $data = null,
        string $message = '',
        string $status,
        int $code = 200,
        array $error = []
    ): void {
        http_response_code($code);
        header('Content-Type: application/json');

        $status = match ($status) {
            'error' => SituacaoEnum::ERROR,
            'alert' => SituacaoEnum::ALERT,
            default => SituacaoEnum::SUCCESS,
        };

        $response = [
            'status' => $status->value,
            'message' => $message,
            'data' => $data,
            'error' => $error
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die;
    }

    public static function success(
        string $message = '',
        ?array $data = null,
        int $code = 200
    ): void {
        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die;
    }

    public static function error(
        string $message = '',
        int $code = 500,
    ): void {
        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die;
    }

    public static function alert(
        string $status = 'alert',
        string $message = '',
        int $code = 200,
        array $errors = []
    ): void {
        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => $status,
            'message' => $message,
            'errors' => [implode(', ', $errors)]
        ];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die;
    }
}
