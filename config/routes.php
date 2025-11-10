<?php
declare(strict_types=1);

use App\Controller\{HomeController, PizzaController, CartController};

return [
    ['GET',  '/',            [HomeController::class, 'index']],
    ['GET',  '/pizzas',      [PizzaController::class, 'index']],

    // API pizza (JSON pour la modale)
    ['GET',  '/api/pizzas/{id}', [PizzaController::class, 'showJson']],

    // Panier
    ['POST', '/cart/add',    [CartController::class, 'add']],   // AJAX (ou fallback POST)
    ['GET',  '/panier',      [CartController::class, 'show']],  // page panier (simple vue)
];