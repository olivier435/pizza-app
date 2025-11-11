<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\UserRepository;

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['user'])) {
            $this->redirect('/checkout');
            return;
        }
        $this->render('auth/login', ['lastEmail' => $_SESSION['_last_email'] ?? '']);
    }

    public function loginPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $email = trim((string)($_POST['email'] ?? ''));
        $pass  = (string)($_POST['password'] ?? '');

        $_SESSION['_last_email'] = $email;

        if ($email === '' || $pass === '') {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Identifiants invalides.'];
            $this->redirect('/login');
            return;
        }

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);

        if (!$user || !password_verify($pass, $user['passwordHash'])) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Email ou mot de passe incorrect.'];
            $this->redirect('/login');
            return;
        }

        // si connexion ok → mise en session
        $_SESSION['user'] = [
            'id'        => (int)$user['id'],
            'email'     => $user['email'],
            'firstname' => $user['firstname'],
            'lastname'  => $user['lastname'],
            'role'      => $user['role'] ?: 'USER',
        ];

        // mise à jour lastLoginAt
        $repo->touchLastLogin((int)$user['id']);

        $target = $_SESSION['_target_path'] ?? '/checkout';
        unset($_SESSION['_target_path']);
        $this->redirect($target);
    }

    public function registerForm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['user'])) {
            $this->redirect('/checkout');
            return;
        }
        $this->render('auth/register', []);
    }

    public function registerPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $email     = trim((string)($_POST['email']     ?? ''));
        $password  = (string)($_POST['password']  ?? '');
        $password2 = (string)($_POST['password2'] ?? '');
        $firstname = trim((string)($_POST['firstname'] ?? ''));
        $lastname  = trim((string)($_POST['lastname']  ?? ''));

        if ($email === '' || $password === '' || $password2 === '' || $firstname === '' || $lastname === '') {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Veuillez remplir tous les champs requis.'];
            $this->redirect('/register');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Email invalide."];
            $this->redirect('/register');
            return;
        }
        if ($password !== $password2) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Les mots de passe ne correspondent pas."];
            $this->redirect('/register');
            return;
        }
        if (strlen($password) < 8) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Mot de passe trop court (8 caractères minimum)."];
            $this->redirect('/register');
            return;
        }

        $repo = new UserRepository();
        if ($repo->findByEmail($email)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Un compte existe déjà avec cet email."];
            $this->redirect('/register');
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $uid  = $repo->createUser([
            'email'        => $email,
            'passwordHash' => $hash,
            'firstname'    => $firstname,
            'lastname'     => $lastname,
            'role'         => 'USER',
        ]);

        if (!$uid) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Impossible de créer le compte pour le moment."];
            $this->redirect('/register');
            return;
        }

        // connexion automatique
        $_SESSION['user'] = [
            'id'        => (int)$uid,
            'email'     => $email,
            'firstname' => $firstname,
            'lastname'  => $lastname,
            'role'      => 'USER',
        ];

        $target = $_SESSION['_target_path'] ?? '/checkout';
        unset($_SESSION['_target_path']);
        $this->redirect($target);
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        unset($_SESSION['user']);
        $this->redirect('/login');
    }
}