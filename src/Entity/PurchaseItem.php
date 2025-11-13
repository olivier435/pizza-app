<?php

declare(strict_types=1);

namespace App\Entity;

final class PurchaseItem extends Entity
{
    private ?int $id = null;
    private int $qty = 1;
    private int $unitPriceCents = 0;
    private int $lineTotalCents = 0;
    private int $pizzaId = 0;
    private int $sizeId = 0;
    private ?int $purchaseId = null;

    private ?string $pizzaName = null;
    private ?string $sizeLabel = null;
    private ?string $pizzaPhoto = null;

    public function __construct(
        ?int $qty = null,
        ?int $unitPriceCents = null,
        ?int $pizzaId = null,
        ?int $sizeId = null
    ) {
        if ($qty !== null) $this->qty = max(1, $qty);
        if ($unitPriceCents !== null) $this->unitPriceCents = max(0, $unitPriceCents);
        if ($pizzaId !== null) $this->pizzaId = $pizzaId;
        if ($sizeId !== null) $this->sizeId = $sizeId;

        $this->recomputeLineTotal();
    }

    // --- Getters / Setters ---
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getQty(): int
    {
        return $this->qty;
    }
    public function setQty(int $q): void
    {
        $this->qty = max(1, $q);
        $this->recomputeLineTotal();
    }

    public function getUnitPriceCents(): int
    {
        return $this->unitPriceCents;
    }
    public function setUnitPriceCents(int $u): void
    {
        $this->unitPriceCents = max(0, $u);
        $this->recomputeLineTotal();
    }

    public function getLineTotalCents(): int
    {
        return $this->lineTotalCents;
    }
    public function setLineTotalCents(int $l): void
    {
        // On autorise l'hydratation DB directe si déjà calculée en DB
        $this->lineTotalCents = max(0, $l);
    }

    public function getPizzaId(): int
    {
        return $this->pizzaId;
    }
    public function setPizzaId(int $id): void
    {
        $this->pizzaId = $id;
    }

    public function getSizeId(): int
    {
        return $this->sizeId;
    }
    public function setSizeId(int $id): void
    {
        $this->sizeId = $id;
    }

    public function getPurchaseId(): ?int
    {
        return $this->purchaseId;
    }
    public function setPurchaseId(int $pid): void
    {
        $this->purchaseId = $pid;
    }

    private function recomputeLineTotal(): void
    {
        $this->lineTotalCents = $this->qty * $this->unitPriceCents;
    }

    public function getPizzaName(): ?string
    {
        return $this->pizzaName;
    }

    public function setPizzaName(?string $name): void
    {
        $this->pizzaName = $name;
    }

    public function getSizeLabel(): ?string
    {
        return $this->sizeLabel;
    }

    public function setSizeLabel(?string $label): void
    {
        $this->sizeLabel = $label;
    }

    public function getPizzaPhoto(): ?string
    {
        return $this->pizzaPhoto;
    }

    public function setPizzaPhoto(?string $photo): void
    {
        $this->pizzaPhoto = $photo;
    }

    public function getPizzaPhotoUrl(): string
    {
        if ($this->pizzaPhoto) {
            return '/assets/img/restaurant/' . ltrim($this->pizzaPhoto, '/');
        }
        return '/assets/img/restaurant/default.webp';
    }
}