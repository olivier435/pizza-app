<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isHome    = ($currentPath === '/');
$isPizzas  = ($currentPath === '/pizzas');
$isCart    = ($currentPath === '/panier');
$isContact    = ($currentPath === '/contact');
$isBooking    = ($currentPath === '/booking');

$anchor = function (string $id) use ($isHome): string {
  return $isHome ? "#{$id}" : "/#{$id}";
};

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

/** Nombre de pizzas DISTINCTES dans le panier */
$cartDistinct = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
  $unique = [];
  foreach ($_SESSION['cart'] as $line) {
    $pid = $line['pizzaId'] ?? ($line['pizza_id'] ?? ($line['pizza']['id'] ?? null));
    if ($pid !== null) {
      $unique[(int)$pid] = true;
    }
  }
  $cartDistinct = count($unique);
}

$isLogged = isset($_SESSION['user']) && !empty($_SESSION['user']['id'] ?? null);
$displayName = '';
if ($isLogged) {
  $fn = (string)($_SESSION['user']['firstname'] ?? '');
  $ln = (string)($_SESSION['user']['lastname'] ?? '');
  $displayName = htmlspecialchars(
    (mb_substr($fn, 0, 1) ? mb_strtoupper(mb_substr($fn, 0, 1)) . '. ' : '')
      . mb_convert_case($ln, MB_CASE_TITLE, 'UTF-8'),
    ENT_QUOTES,
    'UTF-8'
  );
}
?>
<header id="header" class="header fixed-top">
  <div class="topbar d-flex align-items-center dark-background">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center">
          <a href="mailto:contact@example.com">contact@example.com</a>
        </i>
        <i class="bi bi-phone d-flex align-items-center ms-4">
          <a href="tel:+33145239003">+33 1 45 23 90 03</a>
        </i>
      </div>
      <div class="social-links d-none d-md-flex align-items-center">
        <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
        <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
        <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
        <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
      </div>
    </div>
  </div>

  <div class="branding d-flex align-items-center">
    <div class="container position-relative d-flex align-items-center justify-content-between">
      <a href="/" class="logo d-flex align-items-center">
        <img src="/assets/img/logo.webp" alt="Logo Le Papacionu" loading="lazy">
        <h1 class="sitename">le Papacionu</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="/" class="<?= $isHome ? 'active' : '' ?>">Accueil</a></li>
          <li><a href="<?= htmlspecialchars($anchor('about')) ?>">À propos de nous</a></li>
          <li><a href="/pizzas" class="<?= $isPizzas ? 'active' : '' ?>">Notre carte</a></li>
          <li><a href="/booking" class="<?= $isBooking ? 'active' : '' ?>">Réserver</a></li>
          <li><a href="<?= htmlspecialchars($anchor('chefs')) ?>">Notre équipe</a></li>
          <li><a href="/contact" class="<?= $isContact ? 'active' : '' ?>">Contact</a></li>
          <li>
            <a href="/panier" class="header-action-btn position-relative <?= $isCart ? 'active' : '' ?>" aria-label="Voir le panier">
              <i class="bi bi-cart3"></i>
              <?php if ($cartDistinct > 0): ?>
                <span class="badge" aria-live="polite" aria-atomic="true"><?= (int)$cartDistinct ?></span>
                <span class="visually-hidden">articles distincts dans le panier</span>
              <?php endif; ?>
            </a>
          </li>
          <div class="dropdown account-dropdown">
            <button class="header-action-btn ygyyfzzq" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person"></i>
            </button>
            <div class="dropdown-menu">
              <div class="dropdown-header">
                <h6>
                  <?php if ($isLogged): ?>
                    Bonjour <?= $displayName ?: '!' ?>
                  <?php else: ?>
                    Bienvenue au <span class="sitename">Papacionu</span>
                  <?php endif; ?>
                </h6>
                <p class="mb-0">Accéder à votre compte et gérer vos commandes</p>
              </div>
              <div class="dropdown-body">
                <?php if ($isLogged): ?>
                  <a class="dropdown-item d-flex align-items-center" href="/compte">
                    <i class="bi bi-person-circle me-2"></i>
                    <span>Mon Profil</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="/compte?tab=orders">
                    <i class="bi bi-bag-check me-2"></i>
                    <span>Mes Commandes</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="/compte?tab=settings">
                    <i class="bi bi-gear me-2"></i>
                    <span>Paramètres</span>
                  </a>
                <?php else: ?>
                  <a class="dropdown-item d-flex align-items-center" href="/login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    <span>Se connecter</span>
                  </a>
                  <a class="dropdown-item d-flex align-items-center" href="/register">
                    <i class="bi bi-person-plus me-2"></i>
                    <span>Créer un compte</span>
                  </a>
                <?php endif; ?>
              </div>
              <div class="dropdown-footer">
                <?php if ($isLogged): ?>
                  <a href="/logout" class="btn btn-outline-primary w-100">Se déconnecter</a>
                <?php else: ?>
                  <a href="/login" class="btn btn-primary w-100">Se connecter</a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list" aria-label="Menu"></i>
      </nav>
    </div>
  </div>
</header>