<?php
declare(strict_types=1);

use App\Controller\HomeController;

return [
    ['GET', '/', [HomeController::class, 'index']],
];