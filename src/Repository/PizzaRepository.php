<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Pizza;
use PDO;

final class PizzaRepository extends Repository
{
    /** @return Pizza[] */
    public function findAll(): array
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE isActive = 1
                ORDER BY name ASC";

        $stmt  = $this->pdo->query($sql);
        $rows  = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn(array $r) => Pizza::createAndHydrate($r), $rows);
    }

    public function findById(int $id, bool $onlyActive = true): ?Pizza
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Pizza::createAndHydrate($row) : null;
    }

    /** @return Pizza[] */
    public function findRecommended(int $limit = 2): array
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE isActive = 1 AND isRecommended = 1
                ORDER BY name ASC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn(array $r) => Pizza::createAndHydrate($r), $rows);
    }

    public function findOneBySlug(string $slug, bool $onlyActive = true): ?Pizza
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE slug = :slug" . ($onlyActive ? " AND isActive = 1" : "");

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':slug' => $slug]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Pizza::createAndHydrate($row) : null;
    }

    /** @return Pizza[] */
    public function findAllByFilter(string $filter, int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE isActive = 1 AND `filter` = :filter
                ORDER BY name ASC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':filter', $filter, PDO::PARAM_STR);
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $out = [];
        foreach ($rows as $row) {
            $out[] = Pizza::createAndHydrate($row);
        }
        return $out;
    }

    /**
     * Liste complète pour l'admin (actives + inactives)
     * @return Pizza[]
     */
    public function findAllAdmin(): array
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(fn(array $r) => Pizza::createAndHydrate($r), $rows);
    }

    /**
     * Insertion d'une nouvelle pizza
     */
    public function insert(Pizza $pizza): int
    {
        $sql = "INSERT INTO pizza (name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive)
                VALUES (:name, :slug, :description, :photo, :basePriceCents, :isRecommended, :filter, :isActive)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name',            $pizza->getName());
        $stmt->bindValue(':slug',            $pizza->getSlug());
        $stmt->bindValue(':description',     $pizza->getDescription());
        $stmt->bindValue(':photo',           $pizza->getPhoto());
        $stmt->bindValue(':basePriceCents',  $pizza->getBasePriceCents(), PDO::PARAM_INT);
        $stmt->bindValue(':isRecommended',   $pizza->isRecommended() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':filter',          $pizza->getFilter());
        $stmt->bindValue(':isActive',        $pizza->isActive() ? 1 : 0, PDO::PARAM_INT);

        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Mise à jour
     */
    public function update(Pizza $pizza): void
    {
        $sql = "UPDATE pizza
                SET name = :name,
                    slug = :slug,
                    description = :description,
                    photo = :photo,
                    basePriceCents = :basePriceCents,
                    isRecommended = :isRecommended,
                    `filter` = :filter,
                    isActive = :isActive
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id',              $pizza->getId(), PDO::PARAM_INT);
        $stmt->bindValue(':name',            $pizza->getName());
        $stmt->bindValue(':slug',            $pizza->getSlug());
        $stmt->bindValue(':description',     $pizza->getDescription());
        $stmt->bindValue(':photo',           $pizza->getPhoto());
        $stmt->bindValue(':basePriceCents',  $pizza->getBasePriceCents(), PDO::PARAM_INT);
        $stmt->bindValue(':isRecommended',   $pizza->isRecommended() ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':filter',          $pizza->getFilter());
        $stmt->bindValue(':isActive',        $pizza->isActive() ? 1 : 0, PDO::PARAM_INT);

        $stmt->execute();
    }

    /**
     * "Suppression" soft : passe isActive à 0
     */
    public function softDelete(int $id): void
    {
        $sql = "UPDATE pizza SET isActive = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}