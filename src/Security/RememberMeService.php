<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;

final class RememberMeService
{
    public function __construct(private readonly int $days = 7) {}

    public function issue(User $user): void
    {
        $repo = new UserRepository();

        $expires   = new \DateTimeImmutable('+'.$this->days.' days');
        $selector  = bin2hex(random_bytes(9));   // 18 chars → OK avec VARCHAR(24)
        $validator = bin2hex(random_bytes(32));  // 64 chars (secret)
        $hash      = password_hash($validator, PASSWORD_DEFAULT);

        $repo->saveRememberToken((int)$user->getId(), $selector, $hash, $expires);
        $this->setCookie($selector.':'.$validator, $expires);
    }

    public function clearForUserId(int $userId): void
    {
        (new UserRepository())->clearRememberTokenByUserId($userId);
        $this->clearCookie();
    }

    public function tryAutoLogin(): ?User
    {
        if (empty($_COOKIE['REMEMBERME'])) return null;

        $parts = explode(':', $_COOKIE['REMEMBERME'], 2);
        if (count($parts) !== 2) { $this->clearCookie(); return null; }

        [$selector, $validator] = $parts;
        $repo = new UserRepository();
        $user = $repo->findByRememberMe($selector);
        if (!$user) { $this->clearCookie(); return null; }

        $hash   = $user->getRememberTokenHash();
        $expiry = $user->getRememberExpiresAt();
        $notExpired = $expiry instanceof \DateTimeImmutable && $expiry->getTimestamp() > time();

        if (!$hash || !$notExpired || !password_verify($validator, $hash)) {
            $repo->clearRememberTokenByUserId((int)$user->getId());
            $this->clearCookie();
            return null;
        }

        // Rotation du token
        $newSelector  = bin2hex(random_bytes(9));
        $newValidator = bin2hex(random_bytes(32));
        $newHash      = password_hash($newValidator, PASSWORD_DEFAULT);
        $newExpires   = new \DateTimeImmutable('+'.$this->days.' days');

        $repo->saveRememberToken((int)$user->getId(), $newSelector, $newHash, $newExpires);
        $this->setCookie($newSelector.':'.$newValidator, $newExpires);

        return $user;
    }

    public function purgeExpired(): void
    {
        try { (new UserRepository())->purgeExpiredRememberTokens(); } catch (\Throwable) {}
    }

    private function setCookie(string $value, \DateTimeImmutable $expires): void
    {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        setcookie('REMEMBERME', $value, [
            'expires'  => $expires->getTimestamp(),
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE['REMEMBERME'] = $value; // facilite l'enchaînement
    }

    private function clearCookie(): void
    {
        setcookie('REMEMBERME', '', time() - 3600, '/');
        unset($_COOKIE['REMEMBERME']);
    }
}