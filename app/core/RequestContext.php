<?php

namespace App\Core;

class RequestContext
{
    private static array $data = [];

    public static function set(string $key, $value): void
    {
        self::$data[$key] = $value;
    }

    public static function get(string $key)
    {
        return self::$data[$key] ?? null;
    }
}
