<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PizzaIngredientRepository extends Repository
{
    /**
     * Retourne la liste des IDs d'ingrédients associés à une pizza.
     *
     * @return int[]
     */
    public function findIngredientIdsForPizza(int $pizzaId): array
    {
        $sql = "SELECT id_ingredient
                FROM pizza_ingredient
                WHERE id_pizza = :pizza_id";

        $req = $this->pdo->prepare($sql);
        $req->execute([':pizza_id' => $pizzaId]);

        $rows = $req->fetchAll(PDO::FETCH_COLUMN) ?: [];

        return array_map('intval', $rows);
    }

    /**
     * Remplace complètement les ingrédients d'une pizza.
     *
     * @param int   $pizzaId
     * @param int[] $ingredientIds
     */
    public function sync(int $pizzaId, array $ingredientIds): void
    {
        $this->pdo->beginTransaction();
        try {
            // On purge d'abord les anciens liens
            $del = $this->pdo->prepare("
                DELETE FROM pizza_ingredient
                WHERE id_pizza = :pizza_id
            ");
            $del->execute([':pizza_id' => $pizzaId]);

            // Et on ré-insère
            if (!empty($ingredientIds)) {
                $ins = $this->pdo->prepare("
                    INSERT INTO pizza_ingredient (id_ingredient, id_pizza, quantityUnit)
                    VALUES (:id_ingredient, :id_pizza, :quantityUnit)
                ");

                foreach ($ingredientIds as $ingId) {
                    $ingId = (int)$ingId;
                    if ($ingId <= 0) {
                        continue;
                    }

                    $ins->execute([
                        ':id_ingredient' => $ingId,
                        ':id_pizza'      => $pizzaId,
                        ':quantityUnit'  => 100.00,
                    ]);
                }
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function countByIngredient(int $ingredientId): int
    {
        $sql = "SELECT COUNT(*) FROM pizza_ingredient WHERE id_ingredient = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $ingredientId]);

        return (int) $stmt->fetchColumn();
    }
}