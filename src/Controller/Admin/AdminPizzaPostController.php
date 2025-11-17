<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\Admin\AdminBaseController;
use App\Entity\Pizza;
use App\Service\ImageUploader;
use App\Repository\PizzaRepository;
use App\Repository\IngredientRepository;
use App\Service\PizzaDescriptionService;
use App\Repository\PizzaIngredientRepository;

final class AdminPizzaPostController extends AdminBaseController
{
    /**
     * POST /admin/pizzas/new
     */
    public function create(): void
    {
        $this->ensureAdmin();

        $name        = trim((string)($_POST['name'] ?? ''));
        $slugInput   = trim((string)($_POST['slug'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $priceEuro   = str_replace(',', '.', (string)($_POST['priceEuro'] ?? '0'));
        $filter      = (string)($_POST['filter'] ?? 'filter-classic');
        $isRecommended = isset($_POST['isRecommended']);
        $isActive      = isset($_POST['isActive']);

        $removePhoto  = isset($_POST['remove_photo']);
        $currentPhoto = trim((string)($_POST['current_photo'] ?? ''));

        // IDs d'ingrédients cochés
        $ingredientIds = array_map('intval', $_POST['ingredients'] ?? []);

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Le nom est obligatoire.';
        }

        if ($priceEuro === '' || !is_numeric($priceEuro) || (float)$priceEuro <= 0) {
            $errors['priceEuro'] = 'Prix invalide.';
        }

        $allowedFilters = ['filter-classic', 'filter-vegetarian', 'filter-special'];
        if (!in_array($filter, $allowedFilters, true)) {
            $errors['filter'] = 'Filtre invalide.';
        }

        $ingredientRepo = new IngredientRepository();
        $ingredients    = $ingredientRepo->findAll();

        $descSvc   = new PizzaDescriptionService();
        $uploader  = new ImageUploader();
        $photo     = $currentPhoto;

        // Auto-description si pas d'erreurs graves et description vide
        if (empty($errors) && $description === '' && !empty($ingredientIds)) {
            $selectedIngredients = $ingredientRepo->findByIds($ingredientIds);
            $autoDesc            = $descSvc->buildDescriptionFromIngredients($selectedIngredients);
            if ($autoDesc !== '') {
                $description = $autoDesc;
            }
        }

        $basePriceCents = (int)round(((float)$priceEuro) * 100);
        $slugFinal      = $slugInput !== '' ? $slugInput : $this->slugify($name);

        // Upload photo si pas encore d'erreurs
        if (empty($errors)) {
            try {
                if ($removePhoto) {
                    if ($currentPhoto !== '') {
                        $uploader->delete($currentPhoto);
                    }
                    $photo = '';
                } else {
                    // 🔥 On passe le slug pour le nom de fichier
                    $photo = $uploader->uploadAndResize('photoFile', $currentPhoto ?: null, $slugFinal);
                }
            } catch (\RuntimeException $e) {
                $errors['photo'] = $e->getMessage();
            }
        }


        if (!empty($errors)) {
            // On reconstitue une pizza "temporaire" pour réafficher le formulaire
            $pizza = new Pizza();
            $pizza->setName($name)
                ->setSlug($slugFinal)
                ->setDescription($description)
                ->setPhoto($photo !== '' ? $photo : null)
                ->setBasePriceCents($basePriceCents)
                ->setIsRecommended($isRecommended)
                ->setFilter($filter)
                ->setIsActive($isActive);

            $pageTitle = 'Ajouter une pizza';

            $this->render('admin/pizza/form', [
                'pageTitle'             => $pageTitle,
                'pizza'                 => $pizza,
                'errors'                => $errors,
                'mode'                  => 'create',
                'ingredients'           => $ingredients,
                'selectedIngredientIds' => $ingredientIds,
            ]);
            return;
        }

        $repo      = new PizzaRepository();
        $pivotRepo = new PizzaIngredientRepository();
        $pizza     = new Pizza();

        $pizza->setName($name)
            ->setSlug($slugFinal)
            ->setDescription($description)
            ->setPhoto($photo !== '' ? $photo : null)
            ->setBasePriceCents($basePriceCents)
            ->setIsRecommended($isRecommended)
            ->setFilter($filter)
            ->setIsActive($isActive);

        $id = $repo->insert($pizza);

        // Sync des ingrédients
        $pivotRepo->sync($id, $ingredientIds);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Pizza #{$id} créée avec succès.",
        ];

        $this->redirect('/admin/pizzas');
    }

    /**
     * POST /admin/pizzas/{id}/edit
     */
    public function update(array $params): void
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

        $originalSlug = $pizza->getSlug() ?? '';

        $name        = trim((string)($_POST['name'] ?? ''));
        $slugInput   = trim((string)($_POST['slug'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));
        $priceEuro   = str_replace(',', '.', (string)($_POST['priceEuro'] ?? '0'));
        $filter      = (string)($_POST['filter'] ?? 'filter-classic');
        $isRecommended = isset($_POST['isRecommended']);
        $isActive      = isset($_POST['isActive']);

        $removePhoto  = isset($_POST['remove_photo']);
        $currentPhoto = trim((string)($_POST['current_photo'] ?? ''));

        $ingredientIds = array_map('intval', $_POST['ingredients'] ?? []);

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Le nom est obligatoire.';
        }

        if ($priceEuro === '' || !is_numeric($priceEuro) || (float)$priceEuro <= 0) {
            $errors['priceEuro'] = 'Prix invalide.';
        }

        $allowedFilters = ['filter-classic', 'filter-vegetarian', 'filter-special'];
        if (!in_array($filter, $allowedFilters, true)) {
            $errors['filter'] = 'Filtre invalide.';
        }

        $ingredientRepo = new IngredientRepository();
        $pivotRepo      = new PizzaIngredientRepository();
        $ingredients    = $ingredientRepo->findAll();
        $selectedIngredientIds = $pivotRepo->findIngredientIdsForPizza($id);

        $basePriceCents = (int)round(((float)$priceEuro) * 100);

        // Règle sur le slug
        if ($slugInput === '' || $slugInput === $originalSlug) {
            $slugFinal = $this->slugify($name);
        } else {
            $slugFinal = $slugInput;
        }

        $descSvc  = new PizzaDescriptionService();
        $uploader = new ImageUploader();
        $photo    = $currentPhoto;

        // Auto-description si vide + pas encore d'erreurs
        if (empty($errors) && $description === '' && !empty($ingredientIds)) {
            $selectedIngredients = $ingredientRepo->findByIds($ingredientIds);
            $autoDesc            = $descSvc->buildDescriptionFromIngredients($selectedIngredients);
            if ($autoDesc !== '') {
                $description = $autoDesc;
            }
        }

        // Upload / suppression d'image
        if (empty($errors)) {
            try {
                if ($removePhoto) {
                    if ($currentPhoto !== '') {
                        $uploader->delete($currentPhoto);
                    }
                    $photo = '';
                } else {
                    // 🔥 idem ici : nom basé sur le slug
                    $photo = $uploader->uploadAndResize('photoFile', $currentPhoto ?: null, $slugFinal);
                }
            } catch (\RuntimeException $e) {
                $errors['photo'] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $pizza->setName($name)
                ->setSlug($slugFinal)
                ->setDescription($description)
                ->setPhoto($photo !== '' ? $photo : null)
                ->setBasePriceCents($basePriceCents)
                ->setIsRecommended($isRecommended)
                ->setFilter($filter)
                ->setIsActive($isActive);

            $pageTitle = 'Modifier une pizza';

            $this->render('admin/pizza/form', [
                'pageTitle'             => $pageTitle,
                'pizza'                 => $pizza,
                'errors'                => $errors,
                'mode'                  => 'edit',
                'ingredients'           => $ingredients,
                'selectedIngredientIds' => $selectedIngredientIds,
            ]);
            return;
        }

        $pizza->setName($name)
            ->setSlug($slugFinal)
            ->setDescription($description)
            ->setPhoto($photo !== '' ? $photo : null)
            ->setBasePriceCents($basePriceCents)
            ->setIsRecommended($isRecommended)
            ->setFilter($filter)
            ->setIsActive($isActive);

        $repo->update($pizza);
        $pivotRepo->sync($id, $ingredientIds);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Pizza mise à jour.",
        ];

        $this->redirect('/admin/pizzas');
    }

    /**
     * POST /admin/pizzas/{id}/delete
     */
    public function delete(array $params): void
    {
        $this->ensureAdmin();

        $id = (int)($params['id'] ?? 0);
        if ($id <= 0) {
            $this->redirect('/admin/pizzas');
            return;
        }

        $repo = new PizzaRepository();
        $repo->softDelete($id);

        $_SESSION['_flash'][] = [
            'type' => 'success',
            'msg'  => "Pizza archivée (désactivée).",
        ];

        $this->redirect('/admin/pizzas');
    }
}