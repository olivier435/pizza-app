<?php

declare(strict_types=1);

namespace App\Controller;

use Throwable;
use App\Service\Mailer;
use App\Core\Controller;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Service\OrderNumberService;
use App\Repository\PurchaseRepository;
use App\Repository\PurchaseItemRepository;

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

        $this->render('checkout/summary', [
            'cart'  => $cart,
            'total' => $fmt($grand),
            'user'  => $_SESSION['user'],
        ]);
    }

    public function confirm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            $_SESSION['_target_path'] = '/checkout';
            $this->redirect('/login');
            return;
        }
        $userId = (int)($_SESSION['user']['id'] ?? 0);

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            $this->redirect('/panier');
            return;
        }

        $purchaseRepo     = new PurchaseRepository();
        $purchaseItemRepo = new PurchaseItemRepository();
        $numberService    = new OrderNumberService();

        $purchaseRepo->begin();
        try {
            // 1) purchase en statut PENDING
            $purchase = new Purchase($userId);
            $purchase->setNumber('TEMP');
            $purchase->setStatus('PENDING');
            $purchase->setTotalCents(0);

            $purchaseRepo->insertPending($purchase);

            // 2) Lignes de commande
            $grand = 0;
            foreach ($cart as $l) {
                $qty     = (int)($l['qty'] ?? $l['quantity'] ?? 1);
                $line    = (int)($l['totalCents'] ?? 0);
                $unit    = (int)($l['unitPriceCents'] ?? (int)floor($line / max(1, $qty)));
                $pizzaId = (int)($l['pizzaId'] ?? $l['id_pizza'] ?? 0);
                if ($pizzaId <= 0) {
                    throw new \RuntimeException('pizzaId manquant dans le panier.');
                }

                $sizeLabel = (string)($l['size'] ?? $l['sizeLabel'] ?? 'L');
                $sizeId    = $purchaseRepo->resolveSizeId($sizeLabel);

                $item = new PurchaseItem($qty, $unit, $pizzaId, $sizeId);
                $purchaseItemRepo->insert($item, (int)$purchase->getId());
                $purchase->addItem($item);

                $grand += $item->getLineTotalCents();
            }

            // 3) Calcul du total + numéro + statut PAID
            $purchase->setTotalCents($grand);
            $finalNumber = $numberService->generateForId((int)$purchase->getId());
            $purchaseRepo->markPaidAndNumberAndTotal((int)$purchase->getId(), $finalNumber, $purchase->getTotalCents());

            $purchaseRepo->commit();
            $purchase = $purchaseRepo->findWithItems((int)$purchase->getId());

            // 4) vider panier + flash
            unset($_SESSION['cart']);
            try {
                $mailer = new Mailer();
                $user   = $_SESSION['user'] ?? [];
                $fmt    = fn(int $cents) => number_format($cents / 100, 2, ',', ' ') . ' €';

                $mailer->send(
                    $user['email'] ?? 'test@example.com',
                    'Confirmation de votre commande ' . $finalNumber,
                    'order_confirmation',
                    [
                        'purchase' => $purchase,
                        'user'     => $user,
                        'fmt'      => $fmt,
                    ]
                );
            } catch (\Throwable $th) {
                $_SESSION['_flash'][] = [
                    'type' => 'warning',
                    'msg'  => "Commande OK mais e-mail non envoyé."
                ];
            }
            $_SESSION['flash_success'] = "Commande validée : {$finalNumber} (" .
                number_format($grand / 100, 2, ',', ' ') . " €)";

            $this->redirect('/checkout/success?ref=' . urlencode($finalNumber));
        } catch (Throwable $e) {
            $purchaseRepo->rollBack();
            $_SESSION['flash_error'] = "Erreur commande : " . $e->getMessage();
            $this->redirect('/checkout');
        }
    }
}