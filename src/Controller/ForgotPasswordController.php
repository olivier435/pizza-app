<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\UserRepository;
use App\Security\PasswordResetService;
use App\Security\PasswordValidator;

final class ForgotPasswordController extends Controller
{
    // GET /forgot-password — formulaire email
    public function requestForm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $this->render('auth/forgot_request', ['pageTitle' => 'Mot de passe oublié']);
    }

    // POST /forgot-password — traite l'email
    public function requestPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $email = trim((string)($_POST['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Email invalide.'];
            $this->redirect('/forgot-password'); return;
        }

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        // Discrétion : même message qu'il existe ou pas
        if (!$user) {
            $_SESSION['_flash'][] = ['type' => 'success', 'msg' => 'Si un compte existe, un lien de réinitialisation a été envoyé.'];
            $this->redirect('/login'); return;
        }

        $svc = new PasswordResetService(expiryMinutes: 60);
        $url = $svc->createResetRequest($user);

        // TODO: envoyer l'e-mail ; en dev on affiche l'URL
        $_SESSION['_flash'][] = ['type' => 'success', 'msg' => "Lien de réinitialisation généré. (dev) {$url}"];
        $this->redirect('/login');
    }

    /**
     * GET /forgot-password/{selector}/{token}
     */
    public function resetForm(array $params): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $selector = (string)($params['selector'] ?? $params[0] ?? '');
        $token    = (string)($params['token']    ?? $params[1] ?? '');

        $svc  = new PasswordResetService();
        $user = $svc->getUserBySelectorAndToken($selector, $token);

        if (!$user) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Lien invalide ou expiré.'];
            $this->redirect('/login'); return;
        }

        $this->render('auth/reset_password', [
            'pageTitle' => 'Nouveau mot de passe',
            'selector'  => $selector,
            'token'     => $token,
        ]);
    }

    /**
     * POST /forgot-password/{selector}/{token}
     * Idem : on récupère les params via array $params.
     */
    public function resetPost(array $params): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $selector = (string)($params['selector'] ?? $params[0] ?? '');
        $token    = (string)($params['token']    ?? $params[1] ?? '');

        $password  = (string)($_POST['password']  ?? '');
        $password2 = (string)($_POST['password2'] ?? '');

        $svc  = new PasswordResetService();
        $user = $svc->getUserBySelectorAndToken($selector, $token);

        if (!$user) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Lien invalide ou expiré.'];
            $this->redirect('/login'); return;
        }

        if ($password === '' || $password2 === '' || $password !== $password2) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Les mots de passe ne correspondent pas.'];
            $this->redirect("/forgot-password/{$selector}/{$token}"); return;
        }

        // Validation back (force) via PasswordValidator
        $pwErrors = PasswordValidator::validate(
            $password,
            $user->getEmail(),
            $user->getFirstname(),
            $user->getLastname()
        );
        if (!empty($pwErrors)) {
            foreach ($pwErrors as $err) {
                $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => $err];
            }
            $this->redirect("/forgot-password/{$selector}/{$token}"); return;
        }

        if (!$svc->updatePasswordAndClear($user, $password)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Impossible d'enregistrer le nouveau mot de passe."];
            $this->redirect("/forgot-password/{$selector}/{$token}"); return;
        }

        $_SESSION['_flash'][] = ['type' => 'success', 'msg' => 'Mot de passe changé avec succès.'];
        $this->redirect('/login');
    }
}