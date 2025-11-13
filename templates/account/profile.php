<?php
$isOrders   = isset($activeTab) && $activeTab === 'orders';
$isSettings = !$isOrders;
$fmt = fn(int $cents) => number_format($cents / 100, 2, ',', ' ') . ' €';
$orders = $orders ?? [];
$purchaseCount = count($orders);
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Compte</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li class="current">Compte</li>
            </ol>
        </nav>
    </div>
</div>
<section id="account" class="account section" style="background-color: #FFFFFF;">
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
        <div class="mobile-menu d-lg-none mb-4">
            <button class="mobile-menu-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#profileMenu" aria-expanded="false">
                <i class="bi bi-grid"></i>
                <span>Menu</span>
            </button>
        </div>
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="profile-menu collapse d-lg-block" id="profileMenu">
                    <nav class="menu-nav">
                        <ul class="nav flex-column" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= $isOrders ? 'active' : '' ?>" data-bs-toggle="tab"
                                    href="#orders" role="tab" aria-selected="<?= $isOrders ? 'true' : 'false' ?>">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Mes commandes</span>
                                    <span class="badge"><?= $purchaseCount ?></span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link <?= $isSettings ? 'active' : '' ?>" data-bs-toggle="tab"
                                    href="#settings" role="tab" aria-selected="<?= $isSettings ? 'true' : 'false' ?>">
                                    <i class="bi bi-gear"></i>
                                    <span>Paramètres du compte</span>
                                </a>
                            </li>
                        </ul>
                        <div class="menu-footer">
                            <a href="/logout" class="logout-link">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Se déconnecter</span>
                            </a>
                        </div>
                    </nav>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="content-area">
                    <div class="tab-content">
                        <?php
                        require_once __DIR__ . '/_orders_partial.php';
                        renderOrdersTab($orders, $fmt, $isOrders);
                        ?>
                        <div class="tab-pane fade <?= $isSettings ? 'show active' : '' ?>" id="settings" role="tabpanel">
                            <div class="section-header aos-init aos-animate" data-aos="fade-up">
                                <h2>Paramètres du compte</h2>
                            </div>
                            <div class="settings-content">
                                <div class="settings-section aos-init aos-animate" data-aos="fade-up">
                                    <h3>Informations personnelles</h3>
                                    <form class="settings-form" action="/compte/settings" method="post" novalidate>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="firstName" class="form-label">Prénom</label>
                                                <input type="text" class="form-control" id="firstName" name="firstname"
                                                    value="<?= htmlspecialchars($user['firstname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="lastName" class="form-label">Nom</label>
                                                <input type="text" class="form-control" id="lastName" name="lastname"
                                                    value="<?= htmlspecialchars($user['lastname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">E-mail</label>
                                                <input type="email" class="form-control" id="email" name="email"
                                                    value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label">Téléphone</label>
                                                <input type="tel" class="form-control" id="phone" name="phone" data-format="fr-phone"
                                                    value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            </div>
                                        </div>
                                        <div class="form-buttons">
                                            <button type="submit" class="btn-save">Enregistrer</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="settings-section aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
                                    <h3>Sécurité</h3>
                                    <form class="settings-form" action="/compte/security" method="post" novalidate>
                                        <div class="row g-3">
                                            <div class="col-md-12">
                                                <label for="currentPassword" class="form-label">Mot de passe actuel</label>
                                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="newPassword" class="form-label">Nouveau mot de passe</label>
                                                <div class="input-group mb-2">
                                                    <input type="password" class="form-control" id="newPassword" name="newPassword" minlength="8" required autocomplete="new-password">
                                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordSec" aria-label="Afficher le mot de passe">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-primary" type="button" id="generate-password-sec" title="Générer un mot de passe fort">
                                                        <i class="bi bi-shuffle"></i> <span>Générer</span>
                                                    </button>
                                                </div>
                                                <span id="entropy-sec" class="badge text-bg-secondary">Très faible</span>
                                                <div class="progress mt-2" style="height:6px;">
                                                    <div id="password-progress-sec" class="progress-bar bg-danger" style="width:5%;"></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="confirmPassword" class="form-label">Confirmer le mot de passe</label>
                                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" minlength="8" required autocomplete="new-password">
                                            </div>
                                        </div>
                                        <div class="form-buttons">
                                            <button type="submit" class="btn-save" id="pw-submit-btn" disabled>Mettre à jour le mot de passe</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="settings-section danger-zone aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">
                                    <h3>Supprimer le compte</h3>
                                    <div class="danger-zone-content">
                                        <p>Une suppression est définitive. Réfléchissez bien avant de confirmer.</p>
                                        <button type="button" class="btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Supprimer mon compte</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal suppression -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="/compte/delete" id="delete-account-form">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    Cette action est irréversible. Vos informations seront supprimées de façon définitive.
                </p>
                <div class="mb-3">
                    <label for="currentPasswordDel" class="form-label">Mot de passe actuel</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="currentPasswordDel" name="currentPassword" required>
                        <button class="btn btn-outline-secondary" type="button" id="toggleDelPassword" aria-label="Afficher le mot de passe">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmDeletion" name="confirm" value="1" required>
                    <label class="form-check-label" for="confirmDeletion">
                        Oui, je comprends et je souhaite supprimer mon compte.
                    </label>
                </div>

                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrfDelete ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteBtn" disabled>Confirmer la suppression</button>
            </div>
        </form>
    </div>
</div>
<script type="module" src="/assets/js/profile.security.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pwd = document.getElementById('currentPasswordDel');
        const ck = document.getElementById('confirmDeletion');
        const btn = document.getElementById('confirmDeleteBtn');
        const tog = document.getElementById('toggleDelPassword');

        function updateSubmit() {
            const ok = (pwd && pwd.value.trim() !== '') && (ck && ck.checked);
            if (btn) btn.toggleAttribute('disabled', !ok);
        }
        pwd && pwd.addEventListener('input', updateSubmit, {
            passive: true
        });
        ck && ck.addEventListener('change', updateSubmit);
        updateSubmit();

        if (tog && pwd) {
            tog.addEventListener('click', () => {
                const show = pwd.type === 'password';
                pwd.type = show ? 'text' : 'password';
                const icon = tog.querySelector('i');
                if (icon) icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
            });
        }
    });
</script>