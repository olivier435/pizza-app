<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;

final class PasswordResetService
{
    public function __construct(
        private readonly int $expiryMinutes = 60 // lien valable 60 minutes
    ) {}

    /**
     * Crée une demande de réinitialisation :
     * - génère selector (token public) + validator (secret)
     * - stocke hash(validator) + date en BDD
     * - retourne l'URL /forgot-password/{selector}/{validator}
     */
    public function createResetRequest(User $user): string
    {
        $selector  = bin2hex(random_bytes(9));    // 18 hexdigits -> OK avec VARCHAR(24)
        $validator = bin2hex(random_bytes(32));   // 64 hexdigits (secret)
        $hash      = password_hash($validator, PASSWORD_DEFAULT);
        $created   = new DateTimeImmutable();

        (new UserRepository())->storeResetToken(
            userId:    (int)$user->getId(),
            selector:  $selector,
            tokenHash: $hash,
            createdAt: $created
        );

        return "/forgot-password/{$selector}/{$validator}";
    }

    /**
     * Vérifie selector+validator, renvoie l'utilisateur si OK et non expiré.
     */
    public function getUserBySelectorAndToken(string $selector, string $validator): ?User
    {
        if ($selector === '' || $validator === '') return null;

        $repo = new UserRepository();
        $user = $repo->findByResetSelector($selector);
        if (!$user) return null;

        // expiration ?
        if ($this->isTokenExpired($user)) return null;

        $hash = $user->getResetTokenHash();
        if (!$hash || !password_verify($validator, $hash)) return null;

        return $user;
    }

    /**
     * true si (now > resetTokenAt + expiryMinutes)
     */
    public function isTokenExpired(User $user): bool
    {
        $created = $user->getResetTokenAt();
        if (!$created instanceof DateTimeImmutable) return true;
        $expiresAt = $created->modify('+' . $this->expiryMinutes . ' minutes');
        return (new DateTimeImmutable()) > $expiresAt;
    }

    /**
     * Met à jour le mot de passe et efface selector/hash/date.
     */
    public function updatePasswordAndClear(User $user, string $plainPassword): bool
    {
        $hash = password_hash($plainPassword, PASSWORD_DEFAULT);
        return (new UserRepository())->updatePasswordAndClearReset((int)$user->getId(), $hash);
    }
}