<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\PurchaseItem;

final class PurchaseItemRepository extends Repository
{
    public function insert(PurchaseItem $item, int $purchaseId): int
    {
        $item->setPurchaseId($purchaseId);

        $sql = "INSERT INTO purchase_item
                (qty, unitPriceCents, lineTotalCents, id_pizza, id_size, id_purchase)
                VALUES (:q, :u, :l, :pizza, :size, :pid)";
        $req = $this->pdo->prepare($sql);
        $req->execute([
            ':q'    => $item->getQty(),
            ':u'    => $item->getUnitPriceCents(),
            ':l'    => $item->getLineTotalCents(),
            ':pizza'=> $item->getPizzaId(),
            ':size' => $item->getSizeId(),
            ':pid'  => $purchaseId,
        ]);

        $id = (int)$this->pdo->lastInsertId();
        $item->setId($id);
        return $id;
    }
}