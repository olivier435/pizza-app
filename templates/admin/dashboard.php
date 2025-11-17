<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Administration</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li class="current">Administration</li>
            </ol>
        </nav>
    </div>
</div>
<section id="admin-dashboard" class="section light-background">
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
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
                            <a class="nav-link d-flex align-items-center mb-1"
                                href="/admin/pizzas">
                                <i class="bi bi-pie-chart-fill me-2"></i>
                                <span>Pizzas</span>
                            </a>
                            <a class="nav-link d-flex align-items-center mb-1"
                                href="/admin/ingredients">
                                <i class="bi bi-list-ul me-2"></i>
                                <span>Ingrédients</span>
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
                    <div class="section-header mb-4" data-aos="fade-up">
                        <h2>Tableau de bord</h2>
                        <p class="mb-0">
                            Bienvenue dans l'espace d'administration.
                            Sélectionnez une section dans le menu de gauche pour gérer les pizzas et les ingrédients.
                        </p>
                    </div>
                    <!-- Pour le moment : simple placeholder type EasyAdmin -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card stat-card h-100" data-aos="fade-up" data-aos-delay="150">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="card-title mb-0">Pizzas</h5>
                                        <i class="bi bi-pie-chart-fill fs-4"></i>
                                    </div>
                                    <p class="text-muted mb-2">
                                        Gestion des pizzas (création, modification, suppression).
                                    </p>
                                    <a href="/admin/pizzas" class="btn btn-sm btn-outline-primary">
                                        Gérer les pizzas
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card stat-card h-100" data-aos="fade-up" data-aos-delay="200">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="card-title mb-0">Ingrédients</h5>
                                        <i class="bi bi-list-ul fs-4"></i>
                                    </div>
                                    <p class="text-muted mb-2">
                                        Gestion de la liste d'ingrédients et association aux pizzas.
                                    </p>
                                    <a href="/admin/ingredients" class="btn btn-sm btn-outline-primary">
                                        Gérer les ingrédients
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card stat-card h-100" data-aos="fade-up" data-aos-delay="200">
                                <div class="card-body">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="card-title mb-0">Commandes</h5>
                                        <i class="bi bi-bag-fill fs-4"></i>
                                    </div>
                                    <p class="text-muted mb-2">
                                        Gestion de la liste des commandes.
                                    </p>
                                    <a href="/admin/purchases" class="btn btn-sm btn-outline-primary">
                                        Gérer les commandes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>