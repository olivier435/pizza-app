<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;
use App\Entity\User;

final class UserRepository extends Repository
{
    private function hydrate(array $row): User
    {
        $u = (new User())
            ->setId((int)$row['id'])
            ->setEmail((string)$row['email'])
            ->setPasswordHash((string)$row['passwordHash'])
            ->setFirstname((string)$row['firstname'])
            ->setLastname((string)$row['lastname'])
            ->setAddress($row['address'] ?? null)
            ->setPostalCode($row['postalCode'] ?? null)
            ->setCity($row['city'] ?? null)
            ->setPhone($row['phone'] ?? null)
            ->setRole((string)$row['role']);

        if (!empty($row['createdAt'])) {
            $u->setCreatedAt(new \DateTimeImmutable((string)$row['createdAt']));
        }
        if (!empty($row['lastLoginAt'])) {
            $u->setLastLoginAt(new \DateTimeImmutable((string)$row['lastLoginAt']));
        }
        if (array_key_exists('rememberMe', $row)) {
            $u->setRememberMe($row['rememberMe'] !== null ? (string)$row['rememberMe'] : null);
        }
        if (array_key_exists('rememberTokenHash', $row)) {
            $u->setRememberTokenHash($row['rememberTokenHash'] !== null ? (string)$row['rememberTokenHash'] : null);
        }
        if (!empty($row['rememberExpiresAt'])) {
            $u->setRememberExpiresAt(new \DateTimeImmutable((string)$row['rememberExpiresAt']));
        }
        return $u;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
        $req = $this->pdo->prepare($sql);
        $req->execute([':email' => strtolower(trim($email))]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function createFromEntity(User $user): ?int
    {
        $sql = "INSERT INTO user
                    (email,passwordHash,firstname,lastname,address,postalCode,city,phone,role,createdAt)
                VALUES
                    (:email,:passwordHash,:firstname,:lastname,:address,:postalCode,:city,:phone,:role,NOW())";
        $req = $this->pdo->prepare($sql);
        $regist = $req->execute([
            ':email'        => $user->getEmail(),
            ':passwordHash' => $user->getPasswordHash(),
            ':firstname'    => $user->getFirstname(),
            ':lastname'     => $user->getLastname(),
            ':address'      => $user->getAddress(),
            ':postalCode'   => $user->getPostalCode(),
            ':city'         => $user->getCity(),
            ':phone'        => $user->getPhone(),
            ':role'         => $user->getRole(),
        ]);
        if (!$regist) return null;
        return (int)$this->pdo->lastInsertId();
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
        return $req->execute([':email' => strtolower(trim($email))]);
    }

    public function saveRememberToken(int $userId, string $selector, string $validatorHash, \DateTimeImmutable $expires): bool
    {
        $sql = "UPDATE user
            SET rememberMe = :sel,
                rememberTokenHash = :hash,
                rememberExpiresAt = :exp
            WHERE id = :id";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            ':sel'  => $selector,
            ':hash' => $validatorHash,
            ':exp'  => $expires->format('Y-m-d H:i:s'),
            ':id'   => $userId,
        ]);
    }

    public function clearRememberTokenByUserId(int $userId): void
    {
        $st = $this->pdo->prepare("
        UPDATE user
        SET rememberMe = NULL, rememberTokenHash = NULL, rememberExpiresAt = NULL
        WHERE id = :id
    ");
        $st->execute([':id' => $userId]);
    }

    public function findByRememberMe(string $selector): ?User
    {
        $st = $this->pdo->prepare("SELECT * FROM user WHERE rememberMe = :sel LIMIT 1");
        $st->execute([':sel' => $selector]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function purgeExpiredRememberTokens(): int
    {
        $st = $this->pdo->prepare("
        UPDATE user
        SET rememberMe = NULL, rememberTokenHash = NULL, rememberExpiresAt = NULL
        WHERE rememberExpiresAt IS NOT NULL AND rememberExpiresAt < NOW()
    ");
        $st->execute();
        return $st->rowCount();
    }
}