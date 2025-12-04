<?php

namespace Core\Http;

class Response
{
    public static function json($data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $status,
            'data'   => $data
        ]);
        exit;
    }

    public static function raw(string $data, int $status = 200)
    {
        http_response_code($status);
        echo $data;
        exit;
    }
}
