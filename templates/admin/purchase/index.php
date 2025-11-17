<?php

/** @var Purchase[] $purchases */
/** @var string $pageTitle */
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li><a href="/admin">Administration</a></li>
                <li class="current">Commandes</li>
            </ol>
        </nav>
    </div>
</div>
<section id="admin-purchases" class="section light-background">
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
                            <a href="/admin/pizzas" class="nav-link d-flex align-items-center mb-1">
                                <i class="bi bi-pie-chart-fill me-2"></i>
                                <span>Pizzas</span>
                            </a>
                            <a href="/admin/purchases" class="nav-link d-flex align-items-center mb-1 active">
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
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body table-responsive">
                        <?php if (empty($purchases)): ?>
                            <div class="alert alert-info">Aucune commande pour le moment.</div>
                        <?php else: ?>
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Client</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Toggle</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchases as $p): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($p->getNumber()) ?></td>
                                            <td><?= htmlspecialchars($p->getCustomerFullname()) ?></td>
                                            <td><?= $p->getCreatedAt()?->format('d/m/Y H:i') ?></td>
                                            <td><?= number_format($p->getTotalCents() / 100, 2, ',', ' ') ?> €</td>
                                            <td>
                                                <span class="badge 
                            <?= $p->getStatus() === 'PAID' ? 'bg-warning' : ($p->getStatus() === 'DELIVERED' ? 'bg-success' : 'bg-secondary') ?>">
                                                    <?= htmlspecialchars($p->getStatus()) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <form action="/admin/purchases/<?= $p->getId() ?>/toggle" method="post">
                                                    <button class="btn btn-sm btn-outline-primary">
                                                        <?= $p->getStatus() === 'PAID' ? '→ DELIVERED' : '→ PAID' ?>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>