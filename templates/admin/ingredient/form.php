<?php
/** @var string $mode */
/** @var ?\App\Entity\Ingredient $ingredient */
/** @var array $old */
/** @var array $errors */

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$isEdit = ($mode === 'edit');

// Valeurs par défaut
$val = function (string $field, $default = '') use ($old, $ingredient, $isEdit) {
    if (array_key_exists($field, $old)) {
        return $old[$field];
    }

    if ($isEdit && $ingredient) {
        switch ($field) {
            case 'name':            return $ingredient->getName();
            case 'unit':            return $ingredient->getUnit();
            case 'extraPriceEuros':
                $cents = $ingredient->getExtraPriceCents();
                return $cents === null ? '' : number_format($cents / 100, 2, ',', '');
            case 'isVegetarian':    return $ingredient->isVegetarian() ? '1' : '0';
            case 'isVegan':         return $ingredient->isVegan() ? '1' : '0';
            case 'hasAllergens':    return $ingredient->hasAllergens() ? '1' : '0';
            case 'isActive':        return $ingredient->isActive() ? '1' : '0';
        }
    }

    // valeurs par défaut en création
    return $default;
};

$hasError = fn(string $field): bool => isset($errors[$field]);
$errorMsg = fn(string $field): string => $errors[$field] ?? '';

$nameValue     = $val('name', '');
$unitValue     = $val('unit', 'GRAM');
$extraValue    = $val('extraPriceEuros', '');
$isVegValue    = $val('isVegetarian', '0');
$isVeganValue  = $val('isVegan', '0');
$hasAllValue   = $val('hasAllergens', '0');
$isActiveValue = $val('isActive', '1');
?>
<section class="inner-hero section dark-background"></section>

<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li><a href="/admin">Administration</a></li>
                <li><a href="/admin/ingredients">Ingrédients</a></li>
                <li class="current"><?= $isEdit ? 'Modifier' : 'Nouveau' ?></li>
            </ol>
        </nav>
    </div>
</div>

<section id="admin-ingredient-form" class="section light-background">
    <?php include __DIR__ . '/../../layout/_flash.php'; ?>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4">
            <!-- Sidebar admin -->
            <div class="col-lg-3">
                <aside class="admin-sidebar card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-speedometer2 me-2"></i>
                            Tableau de bord
                        </h5>

                        <nav class="nav flex-column admin-menu">
                            <a class="nav-link d-flex align-items-center mb-1 <?= $currentPath === '/admin' ? 'active' : '' ?>"
                               href="/admin">
                                <i class="bi bi-grid-fill me-2"></i>
                                <span>Dashboard</span>
                            </a>
                            <hr class="my-3">
                            <span class="text-muted text-uppercase small mb-2 d-block">Gestion</span>
                            <a class="nav-link d-flex align-items-center mb-1 active"
                               href="/admin/ingredients">
                                <i class="bi bi-list-ul me-2"></i>
                                <span>Ingrédients</span>
                            </a>
                            <a class="nav-link d-flex align-items-center mb-1"
                               href="/admin/pizzas">
                                <i class="bi bi-pie-chart-fill me-2"></i>
                                <span>Pizzas</span>
                            </a>
                        </nav>
                    </div>
                </aside>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <div class="content-area">
                    <div class="section-header mb-4">
                        <h2><?= $isEdit ? 'Modifier un ingrédient' : 'Nouvel ingrédient' ?></h2>
                        <p class="mb-0 text-muted">
                            Définissez les propriétés de l'ingrédient (unités, extra, veggie…).
                        </p>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="<?= $isEdit ? '/admin/ingredients/edit' : '/admin/ingredients/new' ?>" method="post" novalidate>
                                <?php if ($isEdit && $ingredient): ?>
                                    <input type="hidden" name="id" value="<?= (int)$ingredient->getId() ?>">
                                <?php endif; ?>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nom</label>
                                        <input
                                            type="text"
                                            id="name"
                                            name="name"
                                            class="form-control<?= $hasError('name') ? ' is-invalid' : '' ?>"
                                            required
                                            value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errorMsg('name'), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="unit" class="form-label">Unité</label>
                                        <select
                                            id="unit"
                                            name="unit"
                                            class="form-select<?= $hasError('unit') ? ' is-invalid' : '' ?>"
                                            required
                                        >
                                            <option value="GRAM"  <?= $unitValue === 'GRAM'  ? 'selected' : '' ?>>Gramme</option>
                                            <option value="ML"    <?= $unitValue === 'ML'    ? 'selected' : '' ?>>Millilitre</option>
                                            <option value="PIECE" <?= $unitValue === 'PIECE' ? 'selected' : '' ?>>Pièce</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errorMsg('unit'), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="extraPriceEuros" class="form-label">
                                            Supplément (extra) €
                                        </label>
                                        <input
                                            type="text"
                                            id="extraPriceEuros"
                                            name="extraPriceEuros"
                                            class="form-control<?= $hasError('extraPriceEuros') ? ' is-invalid' : '' ?>"
                                            placeholder="ex : 1,50"
                                            value="<?= htmlspecialchars($extraValue, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                        <div class="invalid-feedback">
                                            <?= htmlspecialchars($errorMsg('extraPriceEuros'), ENT_QUOTES, 'UTF-8') ?>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="isVegetarian"
                                                name="isVegetarian"
                                                value="1"
                                                <?= $isVegValue === '1' ? 'checked' : '' ?>
                                            >
                                            <label class="form-check-label" for="isVegetarian">
                                                Végétarien
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="isVegan"
                                                name="isVegan"
                                                value="1"
                                                <?= $isVeganValue === '1' ? 'checked' : '' ?>
                                            >
                                            <label class="form-check-label" for="isVegan">
                                                Végan
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="hasAllergens"
                                                name="hasAllergens"
                                                value="1"
                                                <?= $hasAllValue === '1' ? 'checked' : '' ?>
                                            >
                                            <label class="form-check-label" for="hasAllergens">
                                                Contient des allergènes
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                id="isActive"
                                                name="isActive"
                                                value="1"
                                                <?= $isActiveValue === '1' ? 'checked' : '' ?>
                                            >
                                            <label class="form-check-label" for="isActive">
                                                Actif
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4 d-flex justify-content-between">
                                    <a href="/admin/ingredients" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left-short me-1"></i>
                                        Retour à la liste
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i>
                                        <?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'ingrédient' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>