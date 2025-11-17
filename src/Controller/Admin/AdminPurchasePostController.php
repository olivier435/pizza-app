<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\PurchaseRepository;
use App\Controller\Admin\AdminBaseController;

final class AdminPurchasePostController extends AdminBaseController
{
    public function toggleStatus(array $params): void
    {
        $this->ensureAdmin();

        $id = (int)$params['id'];
        $repo = new PurchaseRepository();

        $purchase = $repo->find($id);
        if (!$purchase) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Commande introuvable"];
            $this->redirect('/admin/purchases');
            return;
        }

        $current = strtoupper($purchase->getStatus());
        $new     = $current === 'PAID' ? 'DELIVERED' : 'PAID';

        $repo->updateStatus($id, $new);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Commande #{$purchase->getNumber()} → $new"
        ];

        $this->redirect('/admin/purchases');
    }
}