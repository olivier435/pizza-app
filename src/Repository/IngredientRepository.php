<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;

final class IngredientRepository extends Repository
{
    /** ingrédients actifs avec un prix extra défini */
    public function findAllExtrasActive(): array
    {
        $sql = "SELECT id, name, unit, COALESCE(extraPriceCents,0) AS extraPriceCents
                FROM ingredient
                WHERE isActive = 1 AND extraPriceCents IS NOT NULL
                ORDER BY name ASC";
        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        // On renvoie déjà des tableaux simples (compatibles JSON)
        return array_map(function($r) {
            $r['id'] = (int)$r['id'];
            $r['extraPriceCents'] = (int)$r['extraPriceCents'];
            return $r;
        }, $rows);
    }
}