<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Core\Controller;
use App\Repository\UserRepository;
use App\Security\PasswordValidator;
use App\Security\RememberMeService;

final class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (isset($_SESSION['user'])) {
            $this->redirect('/checkout');
            return;
        }
        $this->render('auth/login', [
            'lastEmail' => $_SESSION['_last_email'] ?? '',
        ]);
    }

    public function loginPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $email = trim((string)($_POST['email'] ?? ''));
        $pass  = (string)($_POST['password'] ?? '');
        $remember = isset($_POST['remember']) && $_POST['remember'] !== '';

        $_SESSION['_last_email'] = $email;

        if ($email === '' || $pass === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Identifiants invalides.'];
            $this->redirect('/login');
            return;
        }

        $repo = new UserRepository();
        $user = $repo->findByEmail($email);
        if (!$user || !password_verify($pass, $user->getPasswordHash())) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Email ou mot de passe incorrect.'];
            $this->redirect('/login');
            return;
        }

        // si connexion ok → mise en session
        $_SESSION['user'] = $user->toSessionArray();
        $repo->touchLastLogin((int)$user->getId());

        // Remember-me via service
        $rm = new RememberMeService(days: 7);
        if ($remember) {
            $rm->issue($user);
        } else {
            // Nettoie un éventuel cookie existant
            $rm->clearForUserId((int)$user->getId());
        }

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
        $this->render('auth/register');
    }

    public function registerPost(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $email     = trim((string)($_POST['email']     ?? ''));
        $password  = (string)($_POST['password']  ?? '');
        $password2 = (string)($_POST['password2'] ?? '');
        $firstname = (string)($_POST['firstname'] ?? '');
        $lastname  = (string)($_POST['lastname']  ?? '');
        $termsAccepted = isset($_POST['terms']) && (string)$_POST['terms'] !== '';

        // Validations de base
        if ($email === '' || $password === '' || $password2 === '' || $firstname === '' || $lastname === '') {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Veuillez remplir tous les champs requis.'];
            $this->redirect('/register');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Email invalide.'];
            $this->redirect('/register');
            return;
        }
        if ($password !== $password2) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Les mots de passe ne correspondent pas.'];
            $this->redirect('/register');
            return;
        }
        if (!$termsAccepted) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Vous devez accepter les conditions d'utilisation et la politique de confidentialité."];
            $this->redirect('/register');
            return;
        }

        $pwErrors = PasswordValidator::validate($password, $email, $firstname, $lastname);
        if (!empty($pwErrors)) {
            foreach ($pwErrors as $err) {
                $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => $err];
            }
            $this->redirect('/register');
            return;
        }

        $repo = new UserRepository();
        if ($repo->findByEmail($email)) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => 'Un compte existe déjà avec cet email.'];
            $this->redirect('/register');
            return;
        }

        // Construction entité (normalisation prénom/nom dans les setters)
        $user = (new User())
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setRole('USER')
            ->setPasswordHash(password_hash($password, PASSWORD_DEFAULT));

        $id = $repo->createFromEntity($user);
        if (!$id) {
            $_SESSION['_flash'][] = ['type' => 'danger', 'msg' => "Impossible de créer le compte pour le moment."];
            $this->redirect('/register');
            return;
        }

        $user->setId($id);
        $_SESSION['user'] = $user->toSessionArray();

        $target = $_SESSION['_target_path'] ?? '/checkout';
        unset($_SESSION['_target_path']);
        $this->redirect($target);
    }

    public function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!empty($_SESSION['user']['id'])) {
            (new RememberMeService())->clearForUserId((int)$_SESSION['user']['id']);
        } else {
            // Pas d'user en session, on supprime juste le cookie si présent
            setcookie('REMEMBERME', '', time() - 3600, '/');
            unset($_COOKIE['REMEMBERME']);
        }
        unset($_SESSION['user']);
        $this->redirect('/login');
    }
}