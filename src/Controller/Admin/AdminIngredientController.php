<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\IngredientRepository;
use App\Controller\Admin\AdminBaseController;

final class AdminIngredientController extends AdminBaseController
{
    public function index(): void
    {
        $this->ensureAdmin();

        $repo        = new IngredientRepository();
        $ingredients = $repo->findAll();

        $this->render('admin/ingredient/index', [
            'pageTitle'   => 'Ingrédients',
            'ingredients' => $ingredients,
        ]);
    }

    public function new(): void
    {
        $this->ensureAdmin();

        $old    = $_SESSION['ingredient_old']    ?? [];
        $errors = $_SESSION['ingredient_errors'] ?? [];
        unset($_SESSION['ingredient_old'], $_SESSION['ingredient_errors']);

        $this->render('admin/ingredient/form', [
            'pageTitle'  => 'Nouvel ingrédient',
            'mode'       => 'create',
            'ingredient' => null,
            'old'        => $old,
            'errors'     => $errors,
        ]);
    }

    public function edit(): void
    {
        $this->ensureAdmin();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Ingrédient introuvable.',
            ];
            $this->redirect('/admin/ingredients');
            return;
        }

        $repo       = new IngredientRepository();
        $ingredient = $repo->find($id);

        if (!$ingredient) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Ingrédient introuvable.',
            ];
            $this->redirect('/admin/ingredients');
            return;
        }

        $old    = $_SESSION['ingredient_old']    ?? [];
        $errors = $_SESSION['ingredient_errors'] ?? [];
        unset($_SESSION['ingredient_old'], $_SESSION['ingredient_errors']);

        $this->render('admin/ingredient/form', [
            'pageTitle'  => 'Modifier un ingrédient',
            'mode'       => 'edit',
            'ingredient' => $ingredient,
            'old'        => $old,
            'errors'     => $errors,
        ]);
    }
}