<?php

declare(strict_types=1);

use App\Controller\{HomeController, PizzaController, CartController};

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
];