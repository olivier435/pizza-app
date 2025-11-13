<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Entity\Purchase;
use App\Repository\PurchaseRepository;

final class SuccessController extends Controller
{
    public function success(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Vérifie présence du numéro de commande en query string
        $ref = $_GET['ref'] ?? null;
        if (!$ref) {
            $this->redirect('/');
            return;
        }

        // Récupération de la commande pour affichage
        $purchaseRepo = new PurchaseRepository();
        $purchase = $purchaseRepo->findWithItemsByNumber($ref);

        if (!$purchase) {
            $_SESSION['flash_error'] = "Commande introuvable.";
            $this->redirect('/');
            return;
        }

        $fmt = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

        $this->render('checkout/success', [
            'purchase' => $purchase,
            'total'    => $fmt($purchase->getTotalCents()),
            'user'     => $_SESSION['user'] ?? null,
        ]);
    }
}