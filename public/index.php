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
use App\Security\RememberMeService;

// ---- Auto-login Remember Me (si pas déjà connecté) ----
if (!isset($_SESSION['user'])) {
    $rm = new RememberMeService(days: 7);

    // tente l'auto-login via cookie REMEMBERME
    $user = $rm->tryAutoLogin();
    if ($user) {
        $_SESSION['user'] = $user->toSessionArray();
    }

    // purge best-effort des tokens expirés (une fois par session)
    if (!isset($_SESSION['_purged_rm'])) {
        $_SESSION['_purged_rm'] = true;
        $rm->purgeExpired();
    }
}
// -------------------------------------------------------

$router = new Router(require dirname(__DIR__).'/config/routes.php');
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);