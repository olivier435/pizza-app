<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Entity;

class User extends Entity
{
    private ?int $id = null;
    private string $email = '';
    private string $passwordHash = '';
    private string $firstname = '';
    private string $lastname  = '';
    private ?string $address = null;
    private ?string $postalCode = null;
    private ?string $city = null;
    private ?string $phone = null;
    private string $role = 'USER';
    private ?\DateTimeImmutable $createdAt = null;
    private ?\DateTimeImmutable $lastLoginAt = null;

    // ---- Getters / setters ----
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = strtolower(trim($email));
        return $this;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
    public function setPasswordHash(string $hash): self
    {
        $this->passwordHash = $hash;
        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }
    public function setFirstname(string $firstname): self
    {
        $this->firstname = ucfirst(mb_strtolower(trim($firstname)));
        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }
    public function setLastname(string $lastname): self
    {
        $this->lastname = ucfirst(mb_strtolower(trim($lastname)));
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }
    public function setAddress(?string $address): self
    {
        $this->address = $address !== null ? trim($address) : null;
        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode !== null ? trim($postalCode) : null;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }
    public function setCity(?string $city): self
    {
        $this->city = $city !== null ? ucfirst(mb_strtolower(trim($city))) : null;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone !== null ? preg_replace('/\s+/', '', $phone) : null;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }
    public function setRole(string $role): self
    {
        $role = strtoupper(trim($role));
        $this->role = in_array($role, ['ADMIN', 'USER'], true) ? $role : 'USER';
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
    public function setCreatedAt(?\DateTimeImmutable $d): self
    {
        $this->createdAt = $d;
        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeImmutable
    {
        return $this->lastLoginAt;
    }
    public function setLastLoginAt(?\DateTimeImmutable $d): self
    {
        $this->lastLoginAt = $d;
        return $this;
    }

    // ---- Helpers pour session / affichage ----
    public function toSessionArray(): array
    {
        return [
            'id'        => $this->id,
            'email'     => $this->email,
            'firstname' => $this->firstname,
            'lastname'  => $this->lastname,
            'role'      => $this->role,
        ];
    }
}