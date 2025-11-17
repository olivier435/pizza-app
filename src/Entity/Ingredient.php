<?php

declare(strict_types=1);

namespace App\Entity;

final class Ingredient extends Entity
{
    private ?int $id = null;
    private string $name;
    private string $unit = 'GRAM';
    /**
     * Coût d'approvisionnement par unité (nullable, inutilisé pour le moment).
     */
    private ?int $costPerUnitCents = null;
    private bool $isVegetarian = false;
    private bool $isVegan = false;
    private bool $hasAllergens = false;
    private bool $isActive = true;
    /**
     * Prix supplémentaire si l'ingrédient est ajouté en extra sur une pizza (nullable).
     */
    private ?int $extraPriceCents = null;

    public function __construct(
        ?string $name = null,
        ?string $unit = null
    ) {
        if ($name !== null) {
            $this->setName($name);
        }
        if ($unit !== null) {
            $this->setUnit($unit);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = trim($name);
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $unit = strtoupper(trim($unit));
        // Sécurisation minimale : on force sur un set connu
        $allowed = ['GRAM', 'ML', 'PIECE'];
        if (!in_array($unit, $allowed, true)) {
            $unit = 'GRAM';
        }
        $this->unit = $unit;
    }

    public function getCostPerUnitCents(): ?int
    {
        return $this->costPerUnitCents;
    }

    public function setCostPerUnitCents(?int $cents): void
    {
        if ($cents !== null && $cents < 0) {
            $cents = 0;
        }
        $this->costPerUnitCents = $cents;
    }

    public function isVegetarian(): bool
    {
        return $this->isVegetarian;
    }

    public function setIsVegetarian(bool|int $flag): void
    {
        $this->isVegetarian = (bool)$flag;
    }

    public function isVegan(): bool
    {
        return $this->isVegan;
    }

    public function setIsVegan(bool|int $flag): void
    {
        $this->isVegan = (bool)$flag;
    }

    public function hasAllergens(): bool
    {
        return $this->hasAllergens;
    }

    public function setHasAllergens(bool|int $flag): void
    {
        $this->hasAllergens = (bool)$flag;
    }


    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool|int $flag): void
    {
        $this->isActive = (bool)$flag;
    }

    public function getExtraPriceCents(): ?int
    {
        return $this->extraPriceCents;
    }

    public function setExtraPriceCents(?int $cents): void
    {
        if ($cents !== null && $cents < 0) {
            $cents = 0;
        }
        $this->extraPriceCents = $cents;
    }
}