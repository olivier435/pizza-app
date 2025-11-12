<?php
$isOrders   = isset($activeTab) && $activeTab === 'orders';
$isSettings = !$isOrders;
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
                                    <span class="badge">1</span>
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
                        <div class="tab-pane fade <?= $isOrders ? 'show active' : '' ?>" id="orders" role="tabpanel">
                            <div class="section-header aos-init aos-animate" data-aos="fade-up">
                                <h2>Mes commandes</h2>
                            </div>
                            <div class="orders-grid">
                                <!-- Order Card 1 -->
                                <div class="order-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
                                    <div class="order-header">
                                        <div class="order-id">
                                            <span class="label">Order ID:</span>
                                            <span class="value">#ORD-2024-1278</span>
                                        </div>
                                        <div class="order-date">Feb 20, 2025</div>
                                    </div>
                                    <div class="order-content">
                                        <div class="product-grid">
                                            <img src="assets/img/restaurant/napoletana.webp" alt="Product" loading="lazy">
                                        </div>
                                        <div class="order-info">
                                            <div class="info-row">
                                                <span>Status</span>
                                                <span class="status processing">Processing</span>
                                            </div>
                                            <div class="info-row">
                                                <span>Items</span>
                                                <span>3 items</span>
                                            </div>
                                            <div class="info-row">
                                                <span>Total</span>
                                                <span class="price">$789.99</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="order-footer">
                                        <button type="button" class="btn-track" data-bs-toggle="collapse" data-bs-target="#tracking1" aria-expanded="false">Track Order</button>
                                        <button type="button" class="btn-details" data-bs-toggle="collapse" data-bs-target="#details1" aria-expanded="false">View Details</button>
                                    </div>

                                    <!-- Order Tracking -->
                                    <div class="collapse tracking-info" id="tracking1">
                                        <div class="tracking-timeline">
                                            <div class="timeline-item completed">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h5>Order Confirmed</h5>
                                                    <p>Your order has been received and confirmed</p>
                                                    <span class="timeline-date">Feb 20, 2025 - 10:30 AM</span>
                                                </div>
                                            </div>

                                            <div class="timeline-item completed">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h5>Processing</h5>
                                                    <p>Your order is being prepared for shipment</p>
                                                    <span class="timeline-date">Feb 20, 2025 - 2:45 PM</span>
                                                </div>
                                            </div>

                                            <div class="timeline-item active">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h5>Packaging</h5>
                                                    <p>Your items are being packaged for shipping</p>
                                                    <span class="timeline-date">Feb 20, 2025 - 4:15 PM</span>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-truck"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h5>In Transit</h5>
                                                    <p>Expected to ship within 24 hours</p>
                                                </div>
                                            </div>

                                            <div class="timeline-item">
                                                <div class="timeline-icon">
                                                    <i class="bi bi-house-door"></i>
                                                </div>
                                                <div class="timeline-content">
                                                    <h5>Delivery</h5>
                                                    <p>Estimated delivery: Feb 22, 2025</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Order Details -->
                                    <div class="collapse order-details" id="details1">
                                        <div class="details-content">
                                            <div class="detail-section">
                                                <h5>Order Information</h5>
                                                <div class="info-grid">
                                                    <div class="info-item">
                                                        <span class="label">Payment Method</span>
                                                        <span class="value">Credit Card (**** 4589)</span>
                                                    </div>
                                                    <div class="info-item">
                                                        <span class="label">Shipping Method</span>
                                                        <span class="value">Express Delivery (2-3 days)</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="detail-section">
                                                <h5>Items (3)</h5>
                                                <div class="order-items">
                                                    <div class="item">
                                                        <img src="assets/img/product/product-1.webp" alt="Product" loading="lazy">
                                                        <div class="item-info">
                                                            <h6>Lorem ipsum dolor sit amet</h6>
                                                            <div class="item-meta">
                                                                <span class="sku">SKU: PRD-001</span>
                                                                <span class="qty">Qty: 1</span>
                                                            </div>
                                                        </div>
                                                        <div class="item-price">$899.99</div>
                                                    </div>

                                                    <div class="item">
                                                        <img src="assets/img/product/product-2.webp" alt="Product" loading="lazy">
                                                        <div class="item-info">
                                                            <h6>Consectetur adipiscing elit</h6>
                                                            <div class="item-meta">
                                                                <span class="sku">SKU: PRD-002</span>
                                                                <span class="qty">Qty: 2</span>
                                                            </div>
                                                        </div>
                                                        <div class="item-price">$599.95</div>
                                                    </div>

                                                    <div class="item">
                                                        <img src="assets/img/product/product-3.webp" alt="Product" loading="lazy">
                                                        <div class="item-info">
                                                            <h6>Sed do eiusmod tempor</h6>
                                                            <div class="item-meta">
                                                                <span class="sku">SKU: PRD-003</span>
                                                                <span class="qty">Qty: 1</span>
                                                            </div>
                                                        </div>
                                                        <div class="item-price">$129.99</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="detail-section">
                                                <h5>Price Details</h5>
                                                <div class="price-breakdown">
                                                    <div class="price-row">
                                                        <span>Subtotal</span>
                                                        <span>$1,929.93</span>
                                                    </div>
                                                    <div class="price-row">
                                                        <span>Shipping</span>
                                                        <span>$15.99</span>
                                                    </div>
                                                    <div class="price-row">
                                                        <span>Tax</span>
                                                        <span>$159.98</span>
                                                    </div>
                                                    <div class="price-row total">
                                                        <span>Total</span>
                                                        <span>$2,105.90</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="detail-section">
                                                <h5>Shipping Address</h5>
                                                <div class="address-info">
                                                    <p>Sarah Anderson<br>123 Main Street<br>Apt 4B<br>New York, NY 10001<br>United States</p>
                                                    <p class="contact">+1 (555) 123-4567</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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