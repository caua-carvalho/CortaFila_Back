<?php

namespace App\Utils;

class Debug
{
    private const LOG_DIR  = __DIR__ . '/../../storage/logs';
    private const LOG_FILE = self::LOG_DIR . '/app.log';

    /**
     * Registra qualquer dado no log de debug (arrays, strings, objects...)
     */
    public static function log(mixed $data): void
    {
        try {
            if (!is_dir(self::LOG_DIR)) {
                mkdir(self::LOG_DIR, 0777, true);
            }

            $timestamp = date('c'); // ISO 8601
            $entry = [
                'time' => $timestamp,
                'data' => $data
            ];

            file_put_contents(
                self::LOG_FILE,
                json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
                FILE_APPEND
            );
        } catch (\Throwable $e) {
            // fallback: escrever no PHP error_log
            error_log("Debug log failure: " . $e->getMessage());
        }
    }
}
