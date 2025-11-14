<?php

declare(strict_types=1);

use App\Controller\Dev\FixturesController;
use App\Controller\{HomeController, PizzaController, CartController, CheckoutController, AuthController, ForgotPasswordController, ProfileController, AccountDeleteController, SuccessController, ContactController, BookingController};

return [
    ['GET',  '/',            [HomeController::class, 'index']],
    ['GET',  '/pizzas',      [PizzaController::class, 'index']],

    // API pizza (JSON pour la modale)
    ['GET',  '/api/pizzas/{id}', [PizzaController::class, 'showJson']],

    // Panier
    ['GET',  '/panier',     [CartController::class, 'show']],
    ['POST', '/cart/add',   [CartController::class, 'add']],
    ['GET',  '/cart/count', [CartController::class, 'count']],
    ['GET',  '/cart/clear', [CartController::class, 'clear']],
    ['POST', '/cart/update', [CartController::class, 'update']],
    ['POST', '/cart/remove', [CartController::class, 'remove']],
    ['POST', '/cart/edit', [CartController::class, 'edit']],

    // Checkout
    ['GET',  '/checkout', [CheckoutController::class, 'index']],
    ['POST', '/checkout/confirm', [CheckoutController::class, 'confirm']],

    // Success
    ['GET', '/checkout/success', [SuccessController::class, 'success']],

    // Auth
    ['GET',  '/login',    [AuthController::class, 'loginForm']],
    ['POST', '/login',    [AuthController::class, 'loginPost']],
    ['GET',  '/register', [AuthController::class, 'registerForm']],
    ['POST', '/register', [AuthController::class, 'registerPost']],
    ['GET',  '/logout',   [AuthController::class, 'logout']],

    // Fixtures (dev)
    ['GET', '/_fixtures/load',  [FixturesController::class, 'load']],
    ['GET', '/_fixtures/clear', [FixturesController::class, 'clear']],

    // Forgot password
    ['GET',  '/forgot-password',                         [ForgotPasswordController::class, 'requestForm']],
    ['POST', '/forgot-password',                         [ForgotPasswordController::class, 'requestPost']],
    ['GET',  '/forgot-password/{selector}/{token}',      [ForgotPasswordController::class, 'resetForm']],
    ['POST', '/forgot-password/{selector}/{token}',      [ForgotPasswordController::class, 'resetPost']],

    // Profile account
    ['GET',  '/compte',          [ProfileController::class, 'show']],
    ['POST', '/compte/settings',  [ProfileController::class, 'updateSettings']],
    ['POST', '/compte/security',   [ProfileController::class, 'updatePassword']],
    ['POST', '/compte/delete', [AccountDeleteController::class, 'deleteAccount']],

    // Contact
    ['GET', '/contact', [ContactController::class, 'index']],
    ['POST', '/contact', [ContactController::class, 'send']],

    // Booking
    ['GET', '/booking', [BookingController::class, 'index']],
    ['POST', '/booking', [BookingController::class, 'send']],
];