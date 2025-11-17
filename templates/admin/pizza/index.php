<?php

/** @var Pizza[] $pizzas */
/** @var string $pageTitle */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$fmt = fn(int $cents) => number_format($cents / 100, 2, ',', ' ') . ' €';
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li><a href="/admin">Administration</a></li>
                <li class="current">Pizzas</li>
            </ol>
        </nav>
    </div>
</div>
<section id="admin-pizzas" class="section light-background">
    <?php include __DIR__ . '/../../layout/_flash.php'; ?>
    <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-4">
            <!-- Sidebar admin (aligné sur ton dashboard) -->
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
                            <a href="/admin/purchases" class="nav-link d-flex align-items-center mb-1">
                                <i class="bi bi-bag-fill me-2"></i>
                                <span>Commandes</span>
                            </a>
                        </nav>
                    </div>
                </aside>
            </div>
            <div class="col-lg-9">
                <div class="content-area">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="section-header mb-0">
                            <h2><?= htmlspecialchars($pageTitle) ?></h2>
                            <p class="mb-0 text-muted">
                                Gérer la liste des pizzas disponibles (prix, filtres, etc.).
                            </p>
                        </div>
                        <a href="/admin/pizzas/new" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Ajouter une pizza
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body table-responsive">
                        <?php if (empty($pizzas)): ?>
                            <div class="alert alert-info">Aucune pizza pour le moment.</div>
                        <?php else: ?>
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nom</th>
                                        <th>Slug</th>
                                        <th>Prix base (L)</th>
                                        <th>Filtre</th>
                                        <th>Recommandée</th>
                                        <th>Active</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pizzas as $pizza): ?>
                                        <tr>
                                            <td><?= (int)$pizza->getId() ?></td>
                                            <td><?= htmlspecialchars($pizza->getName() ?? '') ?></td>
                                            <td><small class="text-muted"><?= htmlspecialchars($pizza->getSlug() ?? '') ?></small></td>
                                            <td><?= $fmt($pizza->getBasePriceCents()) ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= htmlspecialchars($pizza->getFilter()) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($pizza->isRecommended()): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-star-fill me-1"></i>Oui
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted">Non</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($pizza->isActive()): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-outline-secondary">Inact.</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <a href="/admin/pizzas/<?= (int)$pizza->getId() ?>/edit"
                                                    class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="/admin/pizzas/<?= (int)$pizza->getId() ?>/delete"
                                                    method="post" class="d-inline"
                                                    onsubmit="return confirm('Archiver cette pizza ?');">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-archive"></i>
                                                    </button>
                                                </form>
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
</section>