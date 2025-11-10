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
            // Convertir snake_case ou kebab-case en CamelCase
            $methodName = 'set' . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key)));

            if (method_exists($this, $methodName)) {
                // Auto-conversion pour certains types de colonnes
                if (in_array($key, ['created_at', 'updated_at', 'deleted_at'], true) && !empty($value)) {
                    $value = new DateTimeImmutable($value);
                }

                $this->{$methodName}($value);
            }
        }
    }
}