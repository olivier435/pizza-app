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

        $out = [];
        foreach ($rows as $row) {
            $out[] = Pizza::createAndHydrate($row);
        }
        return $out;
    }

    public function findById(int $id, bool $onlyActive = true): ?Pizza
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE id = :id".($onlyActive ? " AND isActive = 1" : "");

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? Pizza::createAndHydrate($row) : null;
    }

    /** @return Pizza[] */
    public function findRecommended(int $limit = 8): array
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

        $out = [];
        foreach ($rows as $row) {
            $out[] = Pizza::createAndHydrate($row);
        }
        return $out;
    }

    public function findOneBySlug(string $slug, bool $onlyActive = true): ?Pizza
    {
        $sql = "SELECT id, name, slug, description, photo, basePriceCents, isRecommended, `filter`, isActive
                FROM pizza
                WHERE slug = :slug".($onlyActive ? " AND isActive = 1" : "");

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
}