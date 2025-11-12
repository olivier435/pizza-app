<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Core\Controller;
use App\Repository\UserRepository;
use App\Security\PasswordValidator;
use App\Service\UserProfileValidator;

final class ProfileController extends Controller
{
    private UserRepository $repo;
    private UserProfileValidator $validator;
    private PasswordValidator $passwordValidator;

    public function __construct()
    {
        $this->repo = new UserRepository();
        $this->validator = new UserProfileValidator($this->repo);
        $this->passwordValidator = new PasswordValidator();
    }

    public function show(): void
    {
        $sessionUser = $this->getUser();
        if (!$sessionUser) {
            $this->ensureSession();
            $_SESSION['flash'] = "Merci de vous connecter pour accéder à votre profil.";
            $this->redirect('/login');
        }

        $userArray = $this->repo->findByIdBasic((int)$sessionUser['id']);
        if (!$userArray) {
            $this->ensureSession();
            $_SESSION['flash'] = "Compte introuvable.";
            $this->redirect('/login');
        }

        // CSRF suppression
        $this->ensureSession();
        $csrfDelete = bin2hex(random_bytes(16));
        $_SESSION['csrf_delete'] = $csrfDelete;

        $tab = isset($_GET['tab']) ? (string)$_GET['tab'] : 'settings';
        $allowed = ['settings', 'orders'];
        if (!in_array($tab, $allowed, true)) $tab = 'settings';

        $this->render('account/profile', [
            'pageTitle' => 'Mon compte',
            'user'      => $userArray,
            'activeTab' => $tab,
            'csrfDelete' => $csrfDelete,
        ]);
    }

    public function updateSettings(): void
    {
        $sessionUser = $this->getUser();
        if (!$sessionUser) {
            $this->ensureSession();
            $_SESSION['flash'] = "Merci de vous connecter pour modifier votre profil.";
            $this->redirect('/login');
        }

        $user = $this->repo->findById((int)$sessionUser['id']);
        if (!$user instanceof User) {
            $this->ensureSession();
            $_SESSION['flash'] = "Compte introuvable.";
            $this->redirect('/login');
        }

        $firstname = (string)($_POST['firstname'] ?? '');
        $lastname  = (string)($_POST['lastname']  ?? '');
        $email     = (string)($_POST['email']     ?? '');
        $phone     = (string)($_POST['phone']     ?? '');

        $errors = $this->validator->validateProfileUpdate(
            $firstname,
            $lastname,
            $email,
            $phone,
            (int)$user->getId()
        );

        if ($errors) {
            $this->ensureSession();
            $_SESSION['flash'] = implode(' ', $errors);
            $this->redirect('/compte');
        }

        $user
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setPhone($phone !== '' ? $phone : null);

        $this->repo->updateFromEntity($user);

        $this->ensureSession();
        $_SESSION['user']['firstname'] = $user->getFirstname();
        $_SESSION['user']['lastname']  = $user->getLastname();
        $_SESSION['user']['email']     = $user->getEmail();
        $_SESSION['user']['phone']     = $user->getPhone();

        $_SESSION['flash'] = "Informations mises à jour avec succès.";
        $this->redirect('/compte');
    }

    public function updatePassword(): void
    {
        $sessionUser = $this->getUser();
        if (!$sessionUser) {
            $this->ensureSession();
            $_SESSION['flash'] = "Merci de vous connecter pour modifier votre mot de passe.";
            $this->redirect('/login');
        }

        $user = $this->repo->findById((int)$sessionUser['id']);
        if (!$user instanceof User) {
            $this->ensureSession();
            $_SESSION['flash'] = "Compte introuvable.";
            $this->redirect('/login');
        }

        $current = (string)($_POST['currentPassword'] ?? '');
        $new     = (string)($_POST['newPassword'] ?? '');
        $confirm = (string)($_POST['confirmPassword'] ?? '');

        // 1) contrôles simples
        $errors = [];
        if ($current === '' || $new === '' || $confirm === '') {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if ($new !== $confirm) {
            $errors[] = "Les deux mots de passe ne correspondent pas.";
        }

        // 2) mdp actuel correct ?
        if (!password_verify($current, $user->getPasswordHash())) {
            $errors[] = "Le mot de passe actuel est incorrect.";
        }

        $strengthErrors = $this->passwordValidator->validate($new);
        if (!empty($strengthErrors)) {
            $errors = array_merge($errors, $strengthErrors);
        }

        if (!empty($errors)) {
            $this->ensureSession();
            $_SESSION['flash'] = implode(' ', $errors);
            $this->redirect('/compte');
        }

        $hash = password_hash($new, PASSWORD_DEFAULT);
        $this->repo->updatePasswordById((int)$user->getId(), $hash);

        $this->ensureSession();
        $_SESSION['flash'] = "Mot de passe mis à jour avec succès.";
        $this->redirect('/compte');
    }

    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
}