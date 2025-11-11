<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isHome    = ($currentPath === '/');
$isPizzas  = ($currentPath === '/pizzas');
$isCart    = ($currentPath === '/panier');

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
          <li><a href="#">Réserver</a></li>
          <li><a href="<?= htmlspecialchars($anchor('chefs')) ?>">Notre équipe</a></li>
          <li><a href="<?= htmlspecialchars($anchor('contact')) ?>">Contact</a></li>
          <li>
            <a href="/panier" class="header-action-btn position-relative <?= $isCart ? 'active' : '' ?>" aria-label="Voir le panier">
              <i class="bi bi-cart3"></i>
              <?php if ($cartDistinct > 0): ?>
                <span class="badge" aria-live="polite" aria-atomic="true"><?= (int)$cartDistinct ?></span>
                <span class="visually-hidden">articles distincts dans le panier</span>
              <?php endif; ?>
            </a>
          </li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list" aria-label="Menu"></i>
      </nav>
    </div>
  </div>
</header>