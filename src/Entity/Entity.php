<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

abstract class Entity
{
    /**
     * Crée une instance de la classe enfant et hydrate ses propriétés
     */
    public static function createAndHydrate(array $data): static
    {
        $entity = new static();
        $entity->hydrate($data);
        return $entity;
    }

    /**
     * Hydrate les propriétés à partir d'un tableau associatif (PDO::FETCH_ASSOC)
     */
    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            // 1) Candidats de nom de setter
            $candidates = [];

            // a) camelCase direct → set + ucfirst(camel)
            $candidates[] = 'set' . ucfirst($key);

            // b) snake/kebab → set + CamelCase
            $snakeCamel = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));
            $candidates[] = $snakeCamel;

            // c) Spécial id_* → set + CamelCase(sans "id_") + 'Id'
            if (str_starts_with($key, 'id_')) {
                $withoutId = substr($key, 3); // ex: pizza
                $candidates[] = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $withoutId))) . 'Id';
            }

            // d) Spécial *_id → set + CamelCase(sans "_id") + 'Id'
            if (str_ends_with($key, '_id')) {
                $withoutSuffix = substr($key, 0, -3); // ex: pizza
                $candidates[] = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $withoutSuffix))) . 'Id';
            }

            // 2) Conversion auto des dates pour created/updated/deleted_at ET leurs variantes camel
            foreach ($candidates as $methodName) {
                if (!method_exists($this, $methodName)) continue;

                $isDateSnake = in_array($key, ['created_at', 'updated_at', 'deleted_at'], true);
                $isDateCamel = in_array($methodName, ['setCreatedAt', 'setUpdatedAt', 'setDeletedAt'], true);

                if (($isDateSnake || $isDateCamel) && !empty($value) && is_string($value)) {
                    $value = new \DateTimeImmutable($value);
                }

                $this->{$methodName}($value);
                break; // premier setter valide utilisé → on passe à la clé suivante
            }
        }
    }
}