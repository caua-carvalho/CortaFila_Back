<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper
{
    private string $secret;
    private string $algo = 'HS256';

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'] ?? throw new \Exception('JWT_SECRET missing');
    }

    public function encode(array $payload): string
    {
        return JWT::encode($payload, $this->secret, $this->algo);
    }

    public function decode(string $token): object
    {
        return JWT::decode($token, new Key($this->secret, $this->algo));
    }
}
