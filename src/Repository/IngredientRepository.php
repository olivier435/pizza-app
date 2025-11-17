<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\Ingredient;

final class IngredientRepository extends Repository
{
    /**
     * Retourne tous les ingrédients (triés par nom).
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM ingredient ORDER BY name ASC";
        $req = $this->pdo->query($sql);

        $rows = $req->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn($row) => Ingredient::createAndHydrate($row), $rows);
    }

    /**
     * Trouve un ingrédient par son ID.
     */
    public function find(int $id): ?Ingredient
    {
        $req = $this->pdo->prepare("SELECT * FROM ingredient WHERE id = :id LIMIT 1");
        $req->execute([':id' => $id]);

        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? Ingredient::createAndHydrate($row) : null;
    }


    /**
     * Insère un nouvel ingrédient.
     */
    public function insert(Ingredient $i): int
    {
        $sql = "INSERT INTO ingredient (name, unit, costPerUnitCents, isVegetarian, isVegan, hasAllergens, isActive, extraPriceCents)
                VALUES (:name, :unit, :cost, :veg, :vegan, :allergens, :active, :extra)";

        $req = $this->pdo->prepare($sql);

        $req->execute([
            ':name'      => $i->getName(),
            ':unit'      => $i->getUnit(),
            ':cost'      => $i->getCostPerUnitCents(),
            ':veg'       => $i->isVegetarian() ? 1 : 0,
            ':vegan'     => $i->isVegan() ? 1 : 0,
            ':allergens' => $i->hasAllergens() ? 1 : 0,
            ':active'    => $i->isActive() ? 1 : 0,
            ':extra'     => $i->getExtraPriceCents(),
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Met à jour un ingrédient existant.
     */
    public function update(Ingredient $i): void
    {
        $sql = "UPDATE ingredient
                SET name = :name,
                    unit = :unit,
                    costPerUnitCents = :cost,
                    isVegetarian = :veg,
                    isVegan = :vegan,
                    hasAllergens = :allergens,
                    isActive = :active,
                    extraPriceCents = :extra
                WHERE id = :id";

        $req = $this->pdo->prepare($sql);

        $req->execute([
            ':name'      => $i->getName(),
            ':unit'      => $i->getUnit(),
            ':cost'      => $i->getCostPerUnitCents(),
            ':veg'       => $i->isVegetarian() ? 1 : 0,
            ':vegan'     => $i->isVegan() ? 1 : 0,
            ':allergens' => $i->hasAllergens() ? 1 : 0,
            ':active'    => $i->isActive() ? 1 : 0,
            ':extra'     => $i->getExtraPriceCents(),
            ':id'        => $i->getId(),
        ]);
    }

    /**
     * Supprime un ingrédient.
     */
    public function delete(int $id): void
    {
        $req = $this->pdo->prepare("DELETE FROM ingredient WHERE id = :id");
        $req->execute([':id' => $id]);
    }

    /** ingrédients actifs avec un prix extra défini */
    public function findAllExtrasActive(): array
    {
        $sql = "SELECT id, name, unit, COALESCE(extraPriceCents,0) AS extraPriceCents
                FROM ingredient
                WHERE isActive = 1 AND extraPriceCents IS NOT NULL
                ORDER BY name ASC";
        $req = $this->pdo->query($sql);
        $rows = $req->fetchAll(PDO::FETCH_ASSOC) ?: [];
        // On renvoie déjà des tableaux simples (compatibles JSON)
        return array_map(function ($r) {
            return [
                'id'              => (int)$r['id'],
                'name'            => $r['name'],
                'unit'            => $r['unit'],
                'extraPriceCents' => (int)$r['extraPriceCents'],
            ];
        }, $rows);
    }

    /**
     * Retourne les ingrédients dont l'id est dans la liste.
     *
     * @param int[] $ids
     * @return Ingredient[]
     */
    public function findByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT *
                FROM ingredient
                WHERE id IN ($placeholders)
                ORDER BY name ASC";

        $req = $this->pdo->prepare($sql);
        $req->execute($ids);

        $rows = $req->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(
            fn(array $r) => Ingredient::createAndHydrate($r),
            $rows
        );
    }
}