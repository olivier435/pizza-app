<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    /**
     * @param string $view chemin relatif sous /templates (ex: 'home/index')
     * @param array  $params variables exposées à la vue
     * @param string $layout layout à utiliser
     */
    protected function render(string $view, array $params = [], string $layout = 'layout/base'): void
    {
        extract($params, EXTR_OVERWRITE);

        // buffer de la vue
        ob_start();
        require dirname(__DIR__, 2) . "/templates/{$view}.php";
        $content = ob_get_clean();

        // layout
        require dirname(__DIR__, 2) . "/templates/{$layout}.php";
    }

    protected function redirect(string $url): void
    {
        header('Location: ' . $url, true, 302);
        exit;
    }

    protected function isGranted(string $role): bool
    {
        return isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? null) === $role;
    }
}