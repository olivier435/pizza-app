<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\Controller;

final class AdminController extends Controller
{
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Accès réservé aux admins
        if (!$this->isGranted('ADMIN')) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Accès réservé à l'administrateur.",
            ];
            $this->redirect('/');
            return;
        }

        $pageTitle = 'Administration';

        $this->render('admin/dashboard', [
            'pageTitle' => $pageTitle,
            'user'      => $_SESSION['user'] ?? null,
        ]);
    }
}