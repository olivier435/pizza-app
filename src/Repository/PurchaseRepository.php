<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use PDO;

final class PurchaseRepository extends Repository
{
    public function insertPending(Purchase $p): int
    {
        $sql = "INSERT INTO purchase (`number`, `status`, `totalCents`, `id_user`)
                VALUES (:number, :status, :total, :uid)";
        $req = $this->pdo->prepare($sql);
        $req->execute([
            ':number' => $p->getNumber(),     // 'TEMP'
            ':status' => $p->getStatus(),     // 'PENDING'
            ':total'  => $p->getTotalCents(), // 0 au départ
            ':uid'    => $p->getUserId(),
        ]);
        $id = (int)$this->pdo->lastInsertId();
        $p->setId($id);
        return $id;
    }

    public function markPaidAndNumberAndTotal(int $id, string $number, int $totalCents): void
    {
        $sql = "UPDATE purchase
                SET `status`='PAID', `number`=:num, `totalCents`=:total
                WHERE id=:id";
        $req = $this->pdo->prepare($sql);
        $req->execute([':num' => $number, ':total' => $totalCents, ':id' => $id]);
    }

    public function find(int $id): ?Purchase
    {
        $req = $this->pdo->prepare("SELECT * FROM purchase WHERE id=:id LIMIT 1");
        $req->execute([':id' => $id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        /** @var Purchase $purchase */
        $purchase = Purchase::createAndHydrate($row);
        return $purchase;
    }

    public function findWithItems(int $id): ?Purchase
    {
        $purchase = $this->find($id);
        if (!$purchase) return null;

        $sql = "
        SELECT 
            pi.*,
            p.name   AS pizza_name,
            s.label  AS size_label
        FROM purchase_item pi
        JOIN pizza p ON p.id = pi.id_pizza
        JOIN size  s ON s.id = pi.id_size
        WHERE pi.id_purchase = :id
        ORDER BY pi.id ASC
    ";
        $it = $this->pdo->prepare($sql);
        $it->execute([':id' => $id]);

        while ($r = $it->fetch(\PDO::FETCH_ASSOC)) {
        // Hydratation : snake_case → setters (grâce à Entity::hydrate)
            /** @var PurchaseItem $item */
            $item = PurchaseItem::createAndHydrate($r);
            $purchase->addItem($item);
        }

        return $purchase;
    }

    public function findByNumber(string $number): ?Purchase
    {
        $req = $this->pdo->prepare("SELECT * FROM purchase WHERE `number` = :ref LIMIT 1");
        $req->execute([':ref' => $number]);
        $row = $req->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        /** @var Purchase $purchase */
        $purchase = Purchase::createAndHydrate($row);
        return $purchase;
    }

    /**
     * Renvoie l'objet Purchase + items à partir du numéro de commande
     */
    public function findWithItemsByNumber(string $number): ?Purchase
    {
        $purchase = $this->findByNumber($number);
        if (!$purchase) return null;

        return $this->findWithItems((int)$purchase->getId());
    }

    // Transactions
    public function begin(): void
    {
        $this->pdo->beginTransaction();
    }
    public function commit(): void
    {
        $this->pdo->commit();
    }
    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    // Utilitaire : label (M/L/XL) -> id_size
    public function resolveSizeId(string $label): int
    {
        $q = $this->pdo->prepare("SELECT id FROM size WHERE label = :l LIMIT 1");
        $q->execute([':l' => $label]);
        $id = (int)($q->fetchColumn() ?: 0);
        if ($id <= 0) {
            throw new \RuntimeException("Taille inconnue: {$label}");
        }
        return $id;
    }

    public function findAllWithItemsByUserId(int $userId): array
    {
        // 1) Purchases du user
        $req = $this->pdo->prepare("
        SELECT *
        FROM purchase
        WHERE id_user = :uid
        ORDER BY id DESC
    ");
        $req->execute([':uid' => $userId]);

        $purchases = [];
        $ids = [];

        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
            /** @var Purchase $p */
            $p = Purchase::createAndHydrate($row);
            $purchases[$p->getId()] = $p;
            $ids[] = (int)$p->getId();
        }

        if (!$ids) {
            return [];
        }

        // 2) Items + nom pizza + taille + photo
        $in = implode(',', array_fill(0, count($ids), '?'));
        $sql = "
        SELECT 
            pi.*,
            p.name   AS pizza_name,
            p.photo  AS pizza_photo,
            s.label  AS size_label
        FROM purchase_item pi
        JOIN pizza p ON p.id = pi.id_pizza
        JOIN size  s ON s.id = pi.id_size
        WHERE pi.id_purchase IN ($in)
        ORDER BY pi.id_purchase ASC, pi.id ASC
    ";
        $it = $this->pdo->prepare($sql);
        $it->execute($ids);

        while ($r = $it->fetch(PDO::FETCH_ASSOC)) {
            /** @var PurchaseItem $item */
            $item = PurchaseItem::createAndHydrate($r);
            $pid  = (int)$r['id_purchase'];
            if (isset($purchases[$pid])) {
                $purchases[$pid]->addItem($item);
            }
        }

        return array_values($purchases);
    }
}