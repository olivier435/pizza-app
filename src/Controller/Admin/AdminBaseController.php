<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Controller;

abstract class AdminBaseController extends Controller
{
    protected function ensureAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!$this->isGranted('ADMIN')) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Accès réservé à l'administrateur.",
            ];
            $this->redirect('/');
            exit;
        }
    }

    /**
     * Petit helper pour générer les slugs (utilisé surtout par les pizzas)
     */
    protected function slugify(string $name): string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        $slug = strtolower($slug ?: $name);
        $slug = preg_replace('~[^a-z0-9]+~', '-', $slug);
        $slug = trim((string)$slug, '-');

        return $slug !== '' ? $slug : 'item-' . uniqid();
    }
}