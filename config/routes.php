<?php

declare(strict_types=1);

use App\Controller\{HomeController, PizzaController};

return [
    ['GET', '/', [HomeController::class, 'index']],
    ['GET', '/pizzas', [PizzaController::class, 'index']],
];