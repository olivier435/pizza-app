<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Pizza;
use App\Repository\PizzaRepository;
use App\Repository\IngredientRepository;
use App\Controller\Admin\AdminBaseController;
use App\Repository\PizzaIngredientRepository;

final class AdminPizzaController extends AdminBaseController
{
    /**
     * GET /admin/pizzas
     */
    public function index(): void
    {
        $this->ensureAdmin();

        $repo      = new PizzaRepository();
        $pizzas    = $repo->findAllAdmin();
        $pageTitle = 'Gestion des pizzas';

        $this->render('admin/pizza/index', [
            'pageTitle' => $pageTitle,
            'pizzas'    => $pizzas,
            'user'      => $_SESSION['user'] ?? null,
        ]);
    }

    /**
     * GET /admin/pizzas/new
     */
    public function new(): void
    {
        $this->ensureAdmin();

        $pageTitle = 'Ajouter une pizza';
        $pizza     = new Pizza();
        $errors    = [];

        $ingredientRepo = new IngredientRepository();
        $ingredients    = $ingredientRepo->findAll();

        $this->render('admin/pizza/form', [
            'pageTitle'             => $pageTitle,
            'pizza'                 => $pizza,
            'errors'                => $errors,
            'mode'                  => 'create',
            'ingredients'           => $ingredients,
            'selectedIngredientIds' => [],
        ]);
    }

    /**
     * GET /admin/pizzas/{id}/edit
     */
    public function edit(array $params): void
    {
        $this->ensureAdmin();

        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/pizzas');
            return;
        }

        $repo  = new PizzaRepository();
        $pizza = $repo->findById($id);

        if (!$pizza) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Pizza introuvable.",
            ];
            $this->redirect('/admin/pizzas');
            return;
        }

        // 🔽 NOUVEAU : charger les ingrédients + ceux de cette pizza
        $ingredientRepo       = new IngredientRepository();
        $pivotRepo            = new PizzaIngredientRepository();

        $ingredients           = $ingredientRepo->findAll();
        $selectedIngredientIds = $pivotRepo->findIngredientIdsForPizza($id);

        $pageTitle = 'Modifier une pizza';

        $this->render('admin/pizza/form', [
            'pageTitle'             => $pageTitle,
            'pizza'                 => $pizza,
            'errors'                => [],
            'mode'                  => 'edit',
            'ingredients'           => $ingredients,
            'selectedIngredientIds' => $selectedIngredientIds,
        ]);
    }
}