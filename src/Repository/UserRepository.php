<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository extends Repository
{
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
        $req = $this->pdo->prepare($sql);
        $req->execute([':email' => $email]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createUser(array $data): ?int
    {
        // Normalisation du rôle
        $role = strtoupper((string)($data['role'] ?? 'USER'));
        if (!in_array($role, ['ADMIN', 'USER'], true)) {
            $role = 'USER';
        }

        $sql = "INSERT INTO user (email, passwordHash, firstname, lastname, address, postalCode, city, phone, role, createdAt)
                VALUES (:email, :passwordHash, :firstname, :lastname, :address, :postalCode, :city, :phone, :role, NOW())";
        $req = $this->pdo->prepare($sql);
        $regist = $req->execute([
            ':email'        => $data['email'],
            ':passwordHash' => $data['passwordHash'],
            ':firstname'    => $data['firstname'],
            ':lastname'     => $data['lastname'],
            ':address'      => $data['address']    ?? null,
            ':postalCode'   => $data['postalCode'] ?? null,
            ':city'         => $data['city']       ?? null,
            ':phone'        => $data['phone']      ?? null,
            ':role'         => $role,
        ]);
        if (!$regist) return null;
        return (int)$this->pdo->lastInsertId();
    }

    public function touchLastLogin(int $id): void
    {
        $sql = "UPDATE user SET lastLoginAt = NOW() WHERE id = :id";
        $req  = $this->pdo->prepare($sql);
        $req->execute([':id' => $id]);
    }

    public function deleteByEmail(string $email): bool
    {
        $req = $this->pdo->prepare("DELETE FROM user WHERE email = :email");
        return $req->execute([':email' => $email]);
    }
}