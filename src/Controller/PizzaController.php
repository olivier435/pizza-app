<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PizzaRepository;

final class PizzaController extends Controller
{
    public function index(): void
    {
        $repo = new PizzaRepository;

        // 2 pizzas recommandées
        $recommended = $repo->findRecommended(2);

        // toutes les pizzas actives
        $all = $repo->findAll();

        $pageTitle = 'Nos Pizzas - Back Pizza';

        $this->render('pizza/index', [
            'pageTitle'   => $pageTitle,
            'recommended' => $recommended,
            'pizzas'      => $all,
        ]);
    }
}