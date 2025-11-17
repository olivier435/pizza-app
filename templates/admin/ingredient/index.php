<?php

/** @var Ingredient[] $ingredients */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$fmtPrice = fn(?int $cents) => $cents === null ? '—' : number_format($cents / 100, 2, ',', ' ') . ' €';
?>
<section class="inner-hero section dark-background"></section>

<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li><a href="/admin">Administration</a></li>
                <li class="current">Ingrédients</li>
            </ol>
        </nav>
    </div>
</div>

<section id="admin-ingredients" class="section light-background">
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
                            <a href="/admin/purchases" class="nav-link d-flex align-items-center mb-1">
                                <i class="bi bi-bag-fill me-2"></i>
                                <span>Commandes</span>
                            </a>
                        </nav>
                    </div>
                </aside>
            </div>

            <!-- Contenu principal -->
            <div class="col-lg-9">
                <div class="content-area">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-header mb-0">
                            <h2>Ingrédients</h2>
                            <p class="mb-0 text-muted">
                                Gérer la liste des ingrédients disponibles (extras, filtres veggie, etc.).
                            </p>
                        </div>
                        <a href="/admin/ingredients/new" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>
                            Ajouter
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body table-responsive">
                            <?php if (empty($ingredients)): ?>
                                <p class="mb-0 text-muted">Aucun ingrédient pour le moment.</p>
                            <?php else: ?>
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Unité</th>
                                            <th>Extra</th>
                                            <th>Veggie</th>
                                            <th>Vegan</th>
                                            <th>Allergènes</th>
                                            <th>Actif</th>
                                            <th style="width: 140px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ingredients as $ingredient): ?>
                                            <tr>
                                                <td><?= (int)$ingredient->getId() ?></td>
                                                <td><?= htmlspecialchars($ingredient->getName()) ?></td>
                                                <td><?= htmlspecialchars($ingredient->getUnit()) ?></td>
                                                <td><?= htmlspecialchars($fmtPrice($ingredient->getExtraPriceCents())) ?></td>
                                                <td>
                                                    <?php if ($ingredient->isVegetarian()): ?>
                                                        <span class="badge text-bg-success">Oui</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-secondary">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($ingredient->isVegan()): ?>
                                                        <span class="badge text-bg-success">Oui</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-secondary">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($ingredient->hasAllergens()): ?>
                                                        <span class="badge text-bg-warning">Oui</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-secondary">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($ingredient->isActive()): ?>
                                                        <span class="badge text-bg-success">Actif</span>
                                                    <?php else: ?>
                                                        <span class="badge text-bg-secondary">Inactif</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="/admin/ingredients/edit?id=<?= (int)$ingredient->getId() ?>"
                                                            class="btn btn-outline-secondary frvvlcyg">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="/admin/ingredients/delete" method="post" onsubmit="return confirm('Supprimer cet ingrédient ?');">
                                                            <input type="hidden" name="id" value="<?= (int)$ingredient->getId() ?>">
                                                            <button type="submit" class="btn btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>