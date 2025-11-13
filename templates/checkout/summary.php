<?php
$fmt = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';
$total = 0;
foreach (($cart ?? []) as $l) $total += (int)($l['totalCents'] ?? 0);
$totalItems = 0;
foreach ($cart as $l) {
  $totalItems += (int)($l['qty'] ?? $l['quantity'] ?? 1);
}
?>
<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
  <div class="container d-lg-flex justify-content-between align-items-center">
    <h1 class="mb-2 mb-lg-0">Paiement</h1>
    <nav class="breadcrumbs">
      <ol>
        <li><a href="/">Accueil</a></li>
        <li class="current">Paiement</li>
      </ol>
    </nav>
  </div>
</div>
<section id="checkout" class="checkout section">
  <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="row">
      <div class="col-lg-7">
        <div class="checkout-container aos-init aos-animate" data-aos="fade-up">
          <form class="checkout-form" action="/checkout/confirm" method="post">
            <div class="checkout-section" id="customer-info">
              <div class="section-header">
                <div class="section-number">1</div>
                <h3>Informations client</h3>
              </div>
              <div class="section-content">
                <div class="row">
                  <div class="col-md-6 form-group">
                    <label for="first-name">Prénom</label>
                    <input type="text"
                      name="first-name"
                      class="form-control"
                      id="first-name"
                      placeholder="Votre prénom"
                      value="<?= htmlspecialchars($user['firstname'] ?? '') ?>"
                      required>
                  </div>
                  <div class="col-md-6 form-group">
                    <label for="last-name">Nom</label>
                    <input type="text"
                      name="last-name"
                      class="form-control"
                      id="last-name"
                      placeholder="Votre nom"
                      value="<?= htmlspecialchars($user['lastname'] ?? '') ?>"
                      required>
                  </div>
                  <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email"
                      class="form-control"
                      name="email"
                      id="email"
                      placeholder="Votre email"
                      value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                      required>
                  </div>
                  <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel"
                      class="form-control"
                      name="phone"
                      id="phone"
                      placeholder="Votre numéro de téléphone"
                      value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="checkout-section" id="order-review">
              <div class="section-header">
                <div class="section-number">2</div>
                <h3>Validation de la commande</h3>
              </div>
              <div class="section-content">
                <div class="form-check terms-check">
                  <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                  <label class="form-check-label" for="terms">
                    J'accepte les <a href="#">conditions générales</a>
                  </label>
                </div>
                <div class="place-order-container">
                  <button type="submit" class="btn btn-primary place-order-btn">
                    <span class="btn-text">Valider la commande</span>
                    <span class="btn-price">Total <?= $fmt($total) ?></span>
                  </button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="order-summary aos-init aos-animate" data-aos="fade-left" data-aos-delay="200">
          <div class="order-summary-header">
            <h3>Récapitulatif</h3>
            <span class="item-count"><?= $totalItems ?> <?= $totalItems > 1 ? 'pizzas' : 'pizza' ?></span>
          </div>
          <div class="order-summary-content">
            <div class="order-items">
              <?php foreach ($cart as $l): ?>
                <div class="order-item">
                  <div class="order-item-image">
                    <img src="<?= htmlspecialchars($l['photo']) ?>" alt="<?= htmlspecialchars($l['name']) ?>" class="img-fluid">
                  </div>
                  <div class="order-item-details">
                    <h4><?= htmlspecialchars($l['name']) ?></h4>
                    <p class="order-item-variant">Taille : <?= htmlspecialchars($l['size']) ?></p>
                    <div class="order-item-price">
                      <span class="quantity"><?= (int)$l['qty'] ?> ×</span>
                      <span class="price"><?= $fmt((int)$l['unitCents']) ?></span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="order-totals">
              <div class="order-subtotal d-flex justify-content-between">
                <span>Sous-total</span>
                <span><?= $fmt($total) ?></span>
              </div>
              <div class="order-total d-flex justify-content-between">
                <span>Total</span>
                <span><?= $fmt($total) ?></span>
              </div>
            </div>
            <div class="secure-checkout">
              <div class="secure-checkout-header">
                <i class="bi bi-shield-lock"></i>
                <span>Nous acceptons</span>
              </div>
              <div class="payment-icons">
                <i class="bi bi-credit-card-2-front"></i>
                <i class="bi bi-credit-card"></i>
                <i class="bi bi-paypal"></i>
                <i class="bi bi-apple"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>