<?php

use App\Entity\Pizza;

/** @var Pizza|null $pizza */
/** @var array $errors */
/** @var string $mode */
/** @var string $pageTitle */
/** @var Ingredient[] $ingredients */
/** @var int[] $selectedIngredientIds */

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$id       = $pizza->getId();
$priceEuro = $pizza->getBasePriceCents() > 0
    ? number_format($pizza->getBasePriceCents() / 100, 2, ',', '')
    : '0,00';
$ingredients           = $ingredients ?? [];
$selectedIngredientIds = $selectedIngredientIds ?? [];
$currentPhoto = $pizza->getPhoto();
if (!isset($pizza) || !$pizza instanceof Pizza) {
    $pizza = new Pizza();
}
$errors = $errors ?? [];
$isEdit = $mode === 'edit';
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li><a href="/admin">Administration</a></li>
                <li><a href="/admin/ingredients">Pizzas</a></li>
                <li class="current"><?= $isEdit ? 'Modifier' : 'Nouveau' ?></li>
            </ol>
        </nav>
    </div>
</div>
<section id="admin-pizza-form" class="section light-background">
    <?php include __DIR__ . '/../../layout/_flash.php'; ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4">
            <div class="col-lg-3">
                <aside class="admin-sidebar card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Tableau de bord
                        </h5>
                        <nav class="nav flex-column admin-menu">
                            <a href="/admin" class="nav-link d-flex align-items-center mb-1 <?= $currentPath === '/admin' ? 'active' : '' ?>">
                                <i class="bi bi-grid-fill me-2"></i>
                                <span>Dashboard</span>
                            </a>
                            <hr class="my-3">
                            <span class="text-muted text-uppercase small mb-2 d-block">Gestion</span>
                            <a href="/admin/ingredients" class="nav-link d-flex align-items-center mb-1">
                                <i class="bi bi-list-ul me-2"></i>
                                <span>Ingrédients</span>
                            </a>
                            <a href="/admin/pizzas" class="nav-link d-flex align-items-center mb-1 active">
                                <i class="bi bi-pie-chart-fill me-2"></i>
                                <span>Pizzas</span>
                            </a>
                        </nav>
                    </div>
                </aside>
            </div>
            <div class="col-lg-9">
                <div class="content-area">
                    <div class="section-header mb-4">
                        <h2><?= $isEdit ? 'Modifier une pizza' : 'Nouvelle pizza' ?></h2>
                        <p class="mb-0 text-muted">
                            Définissez les propriétés de la pizza (nom, prix, catégorie…).
                        </p>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data" action="<?= $isEdit ? '/admin/pizzas/' . (int)$id . '/edit' : '/admin/pizzas/new' ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nom de la pizza</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                        value="<?= htmlspecialchars($pizza->getName() ?? '') ?>"
                                        required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errors['name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6">
                                    <label for="slug" class="form-label">
                                        Slug
                                        <small class="text-muted">(laisser vide pour auto-génération)</small>
                                    </label>
                                    <input
                                        type="text"
                                        name="slug"
                                        id="slug"
                                        class="form-control"
                                        value="<?= htmlspecialchars($pizza->getSlug() ?? '') ?>">
                                </div>

                                <div class="col-md-6">
                                    <label for="priceEuro" class="form-label">Prix de base (taille L)</label>
                                    <div class="input-group">
                                        <input
                                            type="text"
                                            name="priceEuro"
                                            id="priceEuro"
                                            class="form-control <?= isset($errors['priceEuro']) ? 'is-invalid' : '' ?>"
                                            value="<?= $priceEuro ?>"
                                            required>
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <?php if (isset($errors['priceEuro'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= htmlspecialchars($errors['priceEuro']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        Les tailles M / XL pourront être dérivées automatiquement (-3€ / +3€).
                                    </small>
                                </div>

                                <div class="col-md-6">
                                    <label for="filter" class="form-label">Catégorie (filtre)</label>
                                    <?php $currentFilter = $pizza->getFilter() ?? 'filter-classic'; ?>
                                    <select
                                        name="filter"
                                        id="filter"
                                        class="form-select <?= isset($errors['filter']) ? 'is-invalid' : '' ?>">
                                        <option value="filter-classic" <?= $currentFilter === 'filter-classic' ? 'selected' : '' ?>>
                                            Classique
                                        </option>
                                        <option value="filter-vegetarian" <?= $currentFilter === 'filter-vegetarian' ? 'selected' : '' ?>>
                                            Végétarienne
                                        </option>
                                        <option value="filter-special" <?= $currentFilter === 'filter-special' ? 'selected' : '' ?>>
                                            Spéciale
                                        </option>
                                    </select>
                                    <?php if (isset($errors['filter'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= htmlspecialchars($errors['filter']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea
                                        name="description"
                                        id="description"
                                        rows="3"
                                        class="form-control"><?= htmlspecialchars($pizza->getDescription() ?? '') ?></textarea>
                                </div>

                                <?php
                                require_once __DIR__ . '/_photo_partial.php';
                                renderPizzaPhotoField($pizza, $errors);
                                ?>

                                <div class="col-md-3 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="isRecommended"
                                            name="isRecommended"
                                            <?= $pizza->isRecommended() ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isRecommended">
                                            Pizza du chef
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="isActive"
                                            name="isActive"
                                            <?= $pizza->isActive() ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="isActive">
                                            Active sur le site
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-4">
                            <div class="mt-3">
                                <h5>Ingrédients de base</h5>
                                <div class="row">
                                    <?php foreach ($ingredients as $ingredient): ?>
                                        <?php $idIng = (int)$ingredient->getId(); ?>
                                        <div class="col-md-4">
                                            <div class="form-check mb-1">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="ing-<?= $idIng ?>"
                                                    name="ingredients[]"
                                                    value="<?= $idIng ?>"
                                                    <?= in_array($idIng, $selectedIngredientIds, true) ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="ing-<?= $idIng ?>">
                                                    <?= htmlspecialchars($ingredient->getName()) ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <small class="text-muted">
                                    Les quantités (en unité) sont pour l'instant fixées par défaut dans la recette de base.
                                </small>
                            </div>
                            <div class="mt-4 d-flex justify-content-between">
                                <a href="/admin/pizzas" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>
                                    Retour à la liste
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>
                                    <?= $isEdit ? 'Mettre à jour' : 'Créer la pizza' ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const preview = document.getElementById('photo-preview');
        const fileInput = document.getElementById('photoFile');

        if (preview && fileInput) {
            preview.addEventListener('click', () => fileInput.click());
        }
    });
</script>