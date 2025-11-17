<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

final class Purchase extends Entity
{
    private ?int $id = null;
    private string $number = 'TEMP';
    private ?DateTimeImmutable $createdAt = null;
    private string $status = 'PENDING'; // texte libre
    private int $totalCents = 0;
    private int $userId;
    private ?string $customerFirstname = null;
    private ?string $customerLastname = null;

    /** @var PurchaseItem[] */
    private array $items = [];

    public function __construct(?int $userId = null)
    {
        if ($userId !== null) {
            $this->userId = $userId;
        }
        $this->createdAt = new DateTimeImmutable();
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

    public function getNumber(): string
    {
        return $this->number;
    }
    public function setNumber(string $n): void
    {
        $this->number = $n;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable|string $d): void
    {
        if (is_string($d)) {
            $d = new \DateTimeImmutable($d);
        }
        $this->createdAt = $d;
    }


    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $s): void
    {
        $this->status = $s;
    }

    public function getTotalCents(): int
    {
        return $this->totalCents;
    }
    public function setTotalCents(int $c): void
    {
        $this->totalCents = max(0, $c);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
    public function setUserId(int $id): void
    {
        $this->userId = $id;
    }

    /** @return PurchaseItem[] */
    public function getItems(): array
    {
        return $this->items;
    }
    public function addItem(PurchaseItem $item): void
    {
        $this->items[] = $item;
    }

    public function recomputeTotal(): void
    {
        $sum = 0;
        foreach ($this->items as $it) {
            $sum += $it->getLineTotalCents();
        }
        $this->totalCents = $sum;
    }

    public function getCustomerFirstname(): ?string
    {
        return $this->customerFirstname;
    }
    
    public function setCustomerFirstname(?string $v): void
    {
        $this->customerFirstname = $v;
    }

    public function getCustomerLastname(): ?string
    {
        return $this->customerLastname;
    }

    public function setCustomerLastname(?string $v): void
    {
        $this->customerLastname = $v;
    }

    public function getCustomerFullname(): string
    {
        return trim(($this->customerFirstname ?? '') . ' ' . ($this->customerLastname ?? ''));
    }
}