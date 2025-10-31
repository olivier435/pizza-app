<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<array{0:string,1:string,2:array{0:string,1:string}}> */
    private array $routes;

    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function dispatch(string $method, string $uri): void
    {
        // Normalisation
        $method = strtoupper($method === 'HEAD' ? 'GET' : $method);
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $path = rtrim($path, '/') ?: '/';

        $allowedForPath = [];

        foreach ($this->routes as [$m, $pattern, $handler]) {
            $routePath = rtrim($pattern, '/') ?: '/';
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $routePath) . '$#';

            if (!preg_match($regex, $path, $mats)) {
                continue;
            }

            // On a un match de chemin : on retient les méthodes autorisées
            $allowedForPath[] = strtoupper($m);

            // Méthode HTTP ne correspond pas -> on continue (pour 405 plus bas)
            if (strtoupper($m) !== $method) {
                continue;
            }

            // Ok: on appelle le handler
            $params = array_filter($mats, 'is_string', ARRAY_FILTER_USE_KEY);
            [$class, $action] = $handler;

            // Instanciation basique (pas d'IA container ici)
            (new $class())->{$action}($params);
            return;
        }

        if (!empty($allowedForPath)) {
            // Chemin trouvé mais mauvaise méthode
            http_response_code(405);
            header('Allow: ' . implode(', ', array_unique($allowedForPath)));
            echo '405 Method Not Allowed';
            return;
        }

        http_response_code(404);
        echo '404 Not Found';
    }
}