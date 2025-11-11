<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;

final class CheckoutController extends Controller
{
    /**
     * GET /checkout
     * - Si utilisateur non connecté => redirection /login
     * - Si panier vide => redirection /panier
     * - Sinon => affiche un récapitulatif (ou étape 1 de paiement)
     */
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Accès restreint aux utilisateurs connectés (role USER ou ADMIN)
        if (!isset($_SESSION['user'])) {
            // on mémorise la destination pour rediriger après login
            $_SESSION['_target_path'] = '/checkout';
            $this->redirect('/login');
            return;
        }

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect('/panier');
            return;
        }

        // Total
        $grand = 0;
        foreach ($cart as $l) {
            $grand += (int)($l['totalCents'] ?? 0);
        }
        $fmt = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

        // Ici, on pourrait afficher 'templates/checkout/summary.php'
        // Pour l'instant, on affiche un résumé minimal.
        $this->render('checkout/summary', [
            'cart'  => $cart,
            'total' => $fmt($grand),
            'user'  => $_SESSION['user'],
        ]);
    }
}