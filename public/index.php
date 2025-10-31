<?php
declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';
require dirname(__DIR__).'/config/bootstrap.php';

session_start();

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

use App\Core\Router;

$router = new Router(require dirname(__DIR__).'/config/routes.php');
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
