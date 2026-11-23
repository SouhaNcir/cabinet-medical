<?php

declare(strict_types=1);

if (!function_exists('env_value')) {
    /**
     * Lit une variable d'environnement, avec une valeur par défaut si elle est absente.
     */
    function env_value(string $key, string $default = ''): string
    {
        $value = getenv($key);

        return $value === false ? $default : $value;
    }
}

if (!function_exists('load_env_file')) {
    /**
     * Charge un fichier .env (développement local).
     * Une variable déjà définie (par Docker, par exemple) n'est jamais écrasée.
     */
    function load_env_file(string $path): void
    {
        if (!is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if ($name !== '' && getenv($name) === false) {
                putenv($name . '=' . $value);
            }
        }
    }
}

load_env_file(__DIR__ . '/../../.env');

const APP_NAME = 'Cabinet Medical';
const PER_PAGE = 10;

define('APP_ENV', env_value('APP_ENV', 'development'));
define('BASE_URL', env_value('BASE_URL', '/Cabinet/public'));

define('DB_HOST', env_value('DB_HOST', '127.0.0.1'));
define('DB_PORT', env_value('DB_PORT', '3306'));
define('DB_NAME', env_value('DB_NAME', 'cabinet_medical'));
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));