<?php

namespace App\Middlewares;

use App\Core\RequestContext;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware
{
    public function handle()
    {
        $headers = getallheaders();

        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Missing Authorization header']);
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Authorization']);

        try {
            $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
            RequestContext::set('auth_user', (array) $decoded);
        } catch (\Exception $e) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid JWT']);
            exit;
        }
    }
}
