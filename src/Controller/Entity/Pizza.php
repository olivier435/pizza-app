<?php
declare(strict_types=1);

namespace App\Entity;

final class Pizza extends Entity
{
    /** DB columns */
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $slug = null;
    protected ?string $description = null;
    protected ?string $photo = null;                 // ex: "margherita.webp"
    protected int $basePriceCents = 0;               // prix de base (L)
    protected bool $isRecommended = false;           // pizza du chef
    protected string $filter = 'filter-classic';     // classic|vegetarian|special
    protected bool $isActive = true;

    // ---------- Getters ----------
    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function getSlug(): ?string { return $this->slug; }
    public function getDescription(): ?string { return $this->description; }
    public function getPhoto(): ?string { return $this->photo; }
    public function getBasePriceCents(): int { return $this->basePriceCents; }
    public function isRecommended(): bool { return $this->isRecommended; }
    public function getFilter(): string { return $this->filter; }
    public function isActive(): bool { return $this->isActive; }

    // ---------- Setters (avec cast robustes) ----------
    public function setId(?int $id): self
    {
        $this->id = $id === null ? null : (int)$id;
        return $this;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo ?: null;
        return $this;
    }

    public function setBasePriceCents(int|string $basePriceCents): self
    {
        $this->basePriceCents = (int)$basePriceCents;
        return $this;
    }

    public function setIsRecommended(bool|int|string $isRecommended): self
    {
        // accepte 1/0, '1'/'0', 'true'/'false'
        $this->isRecommended = filter_var($isRecommended, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$isRecommended;
        return $this;
    }

    public function setFilter(string $filter): self
    {
        // sécurité légère : normalise sur nos 3 valeurs connues
        $allowed = ['filter-classic','filter-vegetarian','filter-special'];
        $this->filter = in_array($filter, $allowed, true) ? $filter : 'filter-classic';
        return $this;
    }

    public function setIsActive(bool|int|string $isActive): self
    {
        $this->isActive = filter_var($isActive, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? (bool)$isActive;
        return $this;
    }

    // ---------- Helpers pratiques ----------
    /** URL publique vers l'image (si tu utilises /assets/img/restaurant/) */
    public function getPhotoUrl(): ?string
    {
        return $this->photo ? '/assets/img/restaurant/' . $this->photo : null;
    }

    /** Prix formaté en euros (fr-FR) */
    public function getBasePriceEuros(): string
    {
        return number_format($this->basePriceCents / 100, 2, ',', ' ');
    }

    /** Prix pour une taille donnée (stratégie -3€/+3€) */
    public function getPriceForSize(string $sizeLabel): int
    {
        // sizeLabel attendu: 'M' | 'L' | 'XL'
        return match (strtoupper($sizeLabel)) {
            'M'  => max(0, $this->basePriceCents - 300),
            'XL' => $this->basePriceCents + 300,
            default => $this->basePriceCents, // 'L' par défaut
        };
    }
}