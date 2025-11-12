<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\UserRepository;

final class AccountDeleteController extends Controller
{
    public function __construct(private UserRepository $repo = new UserRepository()) {}

    public function deleteAccount(): void
    {
        $user = $this->getUser();
        if (!$user) {
            $this->ensureSession();
            $_SESSION['flash'] = "Veuillez vous connecter.";
            $this->redirect('/login');
        }

        $this->ensureSession();

        // CSRF
        $token = (string)($_POST['csrf'] ?? '');
        if (empty($_SESSION['csrf_delete']) || !hash_equals($_SESSION['csrf_delete'], $token)) {
            $_SESSION['flash'] = "Requête invalide (CSRF).";
            $this->redirect('/compte');
        }
        unset($_SESSION['csrf_delete']);

        // Confirmation + mot de passe
        $confirm = isset($_POST['confirm']) && $_POST['confirm'] === '1';
        $current = (string)($_POST['currentPassword'] ?? '');
        if (!$confirm || $current === '') {
            $_SESSION['flash'] = "Merci de confirmer la suppression et de saisir votre mot de passe.";
            $this->redirect('/compte');
        }

        // Vérifier le mot de passe
        $entity = $this->repo->findById((int)$user['id']);
        if (!$entity || !password_verify($current, $entity->getPasswordHash())) {
            $_SESSION['flash'] = "Mot de passe actuel incorrect.";
            $this->redirect('/compte');
        }

        // Purge remember-me
        $this->repo->clearRememberTokenByUserId((int)$entity->getId());
        if (!empty($_COOKIE['REMEMBERME'])) {
            setcookie('REMEMBERME', '', time() - 3600, '/');
        }

        // Suppression
        $ok = $this->repo->deleteById((int)$entity->getId());
        if (!$ok) {
            $_SESSION['flash'] = "Une erreur est survenue lors de la suppression.";
            $this->redirect('/compte');
        }

        // Flash + reset session
        $_SESSION['flash'] = "Votre compte a été supprimé. Au revoir !";
        $flash = $_SESSION['flash'];
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['flash'] = $flash;

        $this->redirect('/');
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }
}