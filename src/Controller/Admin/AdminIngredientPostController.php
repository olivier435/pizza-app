<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use App\Service\FormValidator;
use App\Repository\IngredientRepository;
use App\Controller\Admin\AdminBaseController;
use App\Repository\PizzaIngredientRepository;

final class AdminIngredientPostController extends AdminBaseController
{
    public function create(): void
    {
        $this->ensureAdmin();

        $data = [
            'name'            => trim((string)($_POST['name'] ?? '')),
            'unit'            => trim((string)($_POST['unit'] ?? 'GRAM')),
            'extraPriceEuros' => trim((string)($_POST['extraPriceEuros'] ?? '')),
            'isVegetarian'    => isset($_POST['isVegetarian']) ? '1' : '0',
            'isVegan'         => isset($_POST['isVegan']) ? '1' : '0',
            'hasAllergens'    => isset($_POST['hasAllergens']) ? '1' : '0',
            'isActive'        => isset($_POST['isActive']) ? '1' : '0',
        ];

        $validator = new FormValidator($data);

        $validator
            ->required('name', 'Merci de renseigner un nom.')
            ->minLength('name', 2, 'Le nom doit contenir au moins 2 caractères.')
            ->required('unit', 'Merci de choisir une unité.');

        // Validation du prix extra (optionnel)
        $extra = $data['extraPriceEuros'];
        if ($extra !== '') {
            $normalized = str_replace(',', '.', $extra);
            if (!is_numeric($normalized)) {
                $validator->minLength('extraPriceEuros', 9999, 'Prix invalide.'); // hack pour forcer une erreur
            }
        }

        $errors = $validator->getErrors();

        if ($validator->hasErrors()) {
            $_SESSION['ingredient_old']    = $data;
            $_SESSION['ingredient_errors'] = $errors;
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Merci de corriger les erreurs du formulaire.',
            ];
            $this->redirect('/admin/ingredients/new');
            return;
        }

        // Conversion du prix en cents
        $extraCents = null;
        if ($extra !== '') {
            $normalized = str_replace(',', '.', $extra);
            $extraCents = (int)round((float)$normalized * 100);
        }

        $ingredient = new Ingredient($data['name'], $data['unit']);
        $ingredient->setIsVegetarian($data['isVegetarian'] === '1');
        $ingredient->setIsVegan($data['isVegan'] === '1');
        $ingredient->setHasAllergens($data['hasAllergens'] === '1');
        $ingredient->setIsActive($data['isActive'] === '1');
        $ingredient->setExtraPriceCents($extraCents);
        // costPerUnitCents inutilisé → on laisse à null

        $repo = new IngredientRepository();
        $id   = $repo->insert($ingredient);
        $ingredient->setId($id);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Ingrédient « {$ingredient->getName()} » créé avec succès.",
        ];

        $this->redirect('/admin/ingredients');
    }

    public function update(): void
    {
        $this->ensureAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
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

        $data = [
            'name'            => trim((string)($_POST['name'] ?? '')),
            'unit'            => trim((string)($_POST['unit'] ?? 'GRAM')),
            'extraPriceEuros' => trim((string)($_POST['extraPriceEuros'] ?? '')),
            'isVegetarian'    => isset($_POST['isVegetarian']) ? '1' : '0',
            'isVegan'         => isset($_POST['isVegan']) ? '1' : '0',
            'hasAllergens'    => isset($_POST['hasAllergens']) ? '1' : '0',
            'isActive'        => isset($_POST['isActive']) ? '1' : '0',
        ];

        $validator = new FormValidator($data);

        $validator
            ->required('name', 'Merci de renseigner un nom.')
            ->minLength('name', 2, 'Le nom doit contenir au moins 2 caractères.')
            ->required('unit', 'Merci de choisir une unité.');

        $extra = $data['extraPriceEuros'];
        if ($extra !== '') {
            $normalized = str_replace(',', '.', $extra);
            if (!is_numeric($normalized)) {
                $validator->minLength('extraPriceEuros', 9999, 'Prix invalide.');
            }
        }

        $errors = $validator->getErrors();

        if ($validator->hasErrors()) {
            $_SESSION['ingredient_old']    = $data;
            $_SESSION['ingredient_errors'] = $errors;

            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Merci de corriger les erreurs du formulaire.',
            ];

            $this->redirect('/admin/ingredients/edit?id=' . $id);
            return;
        }

        $extraCents = null;
        if ($extra !== '') {
            $normalized = str_replace(',', '.', $extra);
            $extraCents = (int)round((float)$normalized * 100);
        }

        $ingredient->setName($data['name']);
        $ingredient->setUnit($data['unit']);
        $ingredient->setIsVegetarian($data['isVegetarian'] === '1');
        $ingredient->setIsVegan($data['isVegan'] === '1');
        $ingredient->setHasAllergens($data['hasAllergens'] === '1');
        $ingredient->setIsActive($data['isActive'] === '1');
        $ingredient->setExtraPriceCents($extraCents);

        $repo->update($ingredient);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Ingrédient « {$ingredient->getName()} » mis à jour avec succès.",
        ];

        $this->redirect('/admin/ingredients');
    }

    public function delete(): void
    {
        $this->ensureAdmin();

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($id <= 0) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Ingrédient introuvable.',
            ];
            $this->redirect('/admin/ingredients');
            return;
        }

        $ingredientRepo = new IngredientRepository();
        $pivotRepo      = new PizzaIngredientRepository();

        // ✅ Vérifier si l'ingrédient est utilisé dans au moins une pizza
        $usageCount = $pivotRepo->countByIngredient($id);

        if ($usageCount > 0) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Cet ingrédient est utilisé dans {$usageCount} pizza(s), suppression impossible.",
            ];
            $this->redirect('/admin/ingredients');
            return;
        }

        // Sinon, on peut le supprimer tranquillement
        $ingredientRepo->delete($id);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => 'Ingrédient supprimé avec succès.',
        ];

        $this->redirect('/admin/ingredients');
    }
}