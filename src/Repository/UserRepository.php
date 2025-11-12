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
        if (array_key_exists('resetSelector', $row)) {
            $u->setResetSelector($row['resetSelector'] !== null ? (string)$row['resetSelector'] : null);
        }
        if (array_key_exists('resetTokenHash', $row)) {
            $u->setResetTokenHash($row['resetTokenHash'] !== null ? (string)$row['resetTokenHash'] : null);
        }
        if (!empty($row['resetTokenAt'])) {
            $u->setResetTokenAt(new \DateTimeImmutable((string)$row['resetTokenAt']));
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
        $req = $this->pdo->prepare($sql);
        return $req->execute([
            ':sel'  => $selector,
            ':hash' => $validatorHash,
            ':exp'  => $expires->format('Y-m-d H:i:s'),
            ':id'   => $userId,
        ]);
    }

    public function clearRememberTokenByUserId(int $userId): void
    {
        $req = $this->pdo->prepare("
        UPDATE user
        SET rememberMe = NULL, rememberTokenHash = NULL, rememberExpiresAt = NULL
        WHERE id = :id
    ");
        $req->execute([':id' => $userId]);
    }

    public function findByRememberMe(string $selector): ?User
    {
        $req = $this->pdo->prepare("SELECT * FROM user WHERE rememberMe = :sel LIMIT 1");
        $req->execute([':sel' => $selector]);
        $row = $req->fetch(\PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function purgeExpiredRememberTokens(): int
    {
        $req = $this->pdo->prepare("
        UPDATE user
        SET rememberMe = NULL, rememberTokenHash = NULL, rememberExpiresAt = NULL
        WHERE rememberExpiresAt IS NOT NULL AND rememberExpiresAt < NOW()
    ");
        $req->execute();
        return $req->rowCount();
    }

    public function storeResetToken(int $userId, string $selector, string $tokenHash, \DateTimeImmutable $createdAt): bool
    {
        $sql = "UPDATE user
            SET resetSelector = :sel,
                resetTokenHash = :hash,
                resetTokenAt = :at
            WHERE id = :id";
        $req = $this->pdo->prepare($sql);
        return $req->execute([
            ':sel'  => $selector,
            ':hash' => $tokenHash,
            ':at'   => $createdAt->format('Y-m-d H:i:s'),
            ':id'   => $userId,
        ]);
    }

    public function findByResetSelector(string $selector): ?User
    {
        $req = $this->pdo->prepare("SELECT * FROM user WHERE resetSelector = :sel LIMIT 1");
        $req->execute([':sel' => $selector]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->hydrate($row) : null;
    }

    public function updatePasswordAndClearReset(int $userId, string $passwordHash): bool
    {
        $sql = "UPDATE user
            SET passwordHash = :ph,
                resetSelector = NULL,
                resetTokenHash = NULL,
                resetTokenAt = NULL
            WHERE id = :id";
        $req = $this->pdo->prepare($sql);
        return $req->execute([
            ':ph' => $passwordHash,
            ':id' => $userId,
        ]);
    }

    public function findByIdBasic(int $id): ?array
    {
        $sql = 'SELECT id, email, firstname, lastname, phone FROM user WHERE id = ?';
        $req  = $this->pdo->prepare($sql);
        $req->execute([$id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function findById(int $id): ?User
    {
        $sql = 'SELECT * FROM user WHERE id = ?';
        $req  = $this->pdo->prepare($sql);
        $req->execute([$id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $this->hydrate($row);
    }

    public function findOneByEmail(string $email): ?User
    {
        $sql = 'SELECT * FROM user WHERE email = ?';
        $req  = $this->pdo->prepare($sql);
        $req->execute([$email]);
        $row = $req->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return $this->hydrate($row);
    }

    /**
     * Vérifie si un email est déjà pris par un autre utilisateur.
     */
    public function isEmailTakenByOther(string $email, int $excludeUserId): bool
    {
        $sql = 'SELECT id FROM user WHERE email = ? AND id <> ? LIMIT 1';
        $req  = $this->pdo->prepare($sql);
        $req->execute([$email, $excludeUserId]);
        return (bool) $req->fetchColumn();
    }

    /**
     * Met à jour (partiellement) un user depuis l'entité.
     */
    public function updateFromEntity(User $u): void
    {
        $sql = 'UPDATE user SET firstname = ?, lastname = ?, email = ?, phone = ? WHERE id = ?';
        $req  = $this->pdo->prepare($sql);
        $req->execute([
            $u->getFirstname(),
            $u->getLastname(),
            $u->getEmail(),
            $u->getPhone(),
            (int)$u->getId(),
        ]);
    }

    public function updatePasswordById(int $id, string $hash): void
    {
        $req = $this->pdo->prepare('UPDATE user SET passwordHash = :h WHERE id = :id');
        $req->execute([':h' => $hash, ':id' => $id]);
    }

    public function deleteById(int $id): bool
    {
        $st = $this->pdo->prepare('DELETE FROM user WHERE id = :id');
        return $st->execute([':id' => $id]);
    }
}