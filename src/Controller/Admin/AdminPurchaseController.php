<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\PurchaseRepository;
use App\Controller\Admin\AdminBaseController;

final class AdminPurchaseController extends AdminBaseController
{
    public function index(): void
    {
        $this->ensureAdmin();

        $repo      = new PurchaseRepository();
        $purchases = $repo->findAllForAdmin(); // méthode ajoutée plus bas

        $this->render('admin/purchase/index', [
            'pageTitle' => 'Commandes',
            'purchases' => $purchases,
        ]);
    }
}