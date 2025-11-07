<?php
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isHome = ($currentPath === '/');
$anchor = fn(string $id) => $isHome ? "#{$id}" : "/#{$id}";
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
        <img src="/assets/img/logo.webp" alt="Logo">
        <h1 class="sitename">le Papacionu</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="/" class="<?= $isHome ? 'active' : '' ?>">Accueil</a></li>
          <li><a href="<?= $anchor('about') ?>">À propos de nous</a></li>
          <li><a href="/pizzas">Notre carte</a></li>
          <li><a href="#">Réserver</a></li>
          <li><a href="<?= $anchor('chefs') ?>">Notre équipe</a></li>
          <li><a href="<?= $anchor('contact') ?>">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
    </div>
  </div>
</header>