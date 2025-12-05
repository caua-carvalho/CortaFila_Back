<?php

function load_env(string $path): void
{
    if (!file_exists($path)) {
        throw new Exception(".env file not found at: {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        // Ignora comentários
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Quebra KEY=VALUE
        [$key, $value] = array_map('trim', explode('=', $line, 2));

        // Remove aspas se existirem
        $value = trim($value, "'\"");

        // Salva
        $_ENV[$key] = $value;
        putenv("{$key}={$value}");
    }
}
