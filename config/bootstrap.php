<?php

declare(strict_types=1);

use Dotenv\Dotenv;

define('APP_ROOT', dirname(__DIR__));
define('PUBLIC_PATH', APP_ROOT . '/public');

// Chargement autoload (sécurité si bootstrap appelé direct)
if (!class_exists(Dotenv::class)) {
    require APP_ROOT . '/vendor/autoload.php';
}

/**
 * 1. Charge .env (toujours présent, versionné)
 */
$dotenv = Dotenv::createMutable(APP_ROOT);
$dotenv->safeLoad(); // Ne plante pas si le fichier est absent

/**
 * 2. Charge .env.local si présent
 *    → surcharge manuelle (remplace les variables déjà définies)
 */
$envLocalPath = APP_ROOT . '/.env.local';
if (is_file($envLocalPath)) {
    $lines = file($envLocalPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        if ($key !== '' && $value !== '') {
            $_ENV[$key] = $_SERVER[$key] = $value;
        }
    }
}

/**
 * Helper env(): lit $_ENV ou $_SERVER, sinon renvoie la valeur par défaut.
 */
if (!function_exists('env')) {
    function env(string $key, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

/**
 * Constantes globales
 */
define('APP_ENV',    env('APP_ENV', 'prod'));
define('APP_SECRET', env('APP_SECRET', 'change-me'));
define('APP_DEBUG',  (int) env('APP_DEBUG', '0') === 1);

define('DB_HOST',    env('DB_HOST', '127.0.0.1'));
define('DB_PORT',    (string) env('DB_PORT', '3306'));
define('DB_NAME',    env('DB_NAME', 'pizza'));
define('DB_USER',    env('DB_USER', 'root'));

$pass = env('DB_PASSWORD', '');
if ($pass === 'null' || $pass === 'NULL') {
    $pass = null;
}
define('DB_PASS', $pass);

define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

define('DB_DSN', sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
    DB_HOST,
    DB_PORT,
    DB_NAME,
    DB_CHARSET
));

/**
 * Contexte applicatif global
 */
mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Paris');

// Sécurité sessions
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) ? '1' : '0'));
ini_set('session.use_strict_mode', '1');