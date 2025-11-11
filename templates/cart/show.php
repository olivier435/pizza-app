<?php
/** @var array $cart */
/** Ex: chaque ligne:
 * [
 *   'pizzaId'=>int, 'name'=>string, 'size'=>'M|L|XL', 'qty'=>int,
 *   'unitCents'=>int, 'totalCents'=>int, 'extras'=>[ ['id'=>int,'name'=>string,'price'=>int], ... ],
 *   'photo'=>string|null
 * ]
 */

$euros = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

// Diamètres "référence" (pour affichage)
$sizeDiameters = ['M' => 28, 'L' => 33, 'XL' => 40];

$grand = 0;
foreach ($cart ?? [] as $l) {
  $grand += (int)($l['totalCents'] ?? 0);
}
?>

<section class="inner-hero section dark-background"></section>

<section id="cart" class="cart section">
  <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">
    <div class="row g-4">
      <div class="col-lg-8 aos-init aos-animate" data-aos="fade-up" data-aos-delay="200">
        <div class="cart-items">
          <?php if (empty($cart)): ?>
            <p class="text-muted">Votre panier est vide.</p>
          <?php else: ?>
            <div class="cart-header d-none d-lg-block">
                <div class="row align-items-center gy-4">
                  <div class="col-lg-6">
                    <h5>Pizzas</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Prix</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Quantité</h5>
                  </div>
                  <div class="col-lg-2 text-center">
                    <h5>Total</h5>
                  </div>
                </div>
            </div>
            <?php foreach ($cart as $idx => $line): 
              $name  = (string)($line['name'] ?? '');
              $size  = (string)($line['size'] ?? 'L');
              $qty   = (int)($line['qty'] ?? 1);
              $unit  = (int)($line['unitCents'] ?? 0);
              $total = (int)($line['totalCents'] ?? ($unit * $qty));
              $photo = (string)($line['photo'] ?? '/assets/img/restaurant/default.webp');
              $diam  = $sizeDiameters[$size] ?? null;
              // extras formatés
              $extrasTxt = '';
              if (!empty($line['extras']) && is_array($line['extras'])) {
                $names = array_map(fn($e) => (string)$e['name'], $line['extras']);
                if ($names) {
                  $extrasTxt = '+ ' . htmlspecialchars(implode(', ', $names));
                }
              }
            ?>
              <!-- Cart Item -->
              <div class="cart-item aos-init aos-animate" data-index="<?= (int)$idx ?>" data-pizza-id="<?= (int)($line['pizzaId'] ?? 0) ?>">
                <div class="row align-items-center gy-4">
                  <div class="col-lg-6 col-12 mb-3 mb-lg-0">
                    <div class="product-info d-flex align-items-center">
                      <div class="product-image">
                        <img src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($name) ?>" class="img-fluid" loading="lazy">
                      </div>
                      <div class="product-details">
                        <h6 class="product-title"><?= htmlspecialchars($name) ?></h6>
                        <div class="product-meta">
                          <span class="product-size">Taille&nbsp;: <?= htmlspecialchars($size) ?><?= $diam ? " - {$diam}cm" : '' ?></span>
                          <?php if ($extrasTxt): ?>
                            <span class="d-block"><?= $extrasTxt ?></span>
                          <?php endif; ?>
                        </div>
                        <!-- Bouton Modifier (Bootstrap) -->
                        <button
                          type="button"
                          class="edit-item"
                          data-index="<?= (int)$idx ?>"
                          data-pizza-id="<?= (int)($line['pizzaId'] ?? 0) ?>"
                          aria-label="Modifier taille et ingrédients">
                          <i class="bi bi-pencil-square"></i> Modifier
                        </button>
                        <button class="remove-item" type="button" data-index="<?= (int)$idx ?>">
                          <i class="bi bi-trash"></i> Supprimer
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="col-12 col-lg-2 text-center">
                    <div class="price-tag">
                      <span class="current-price"><?= $euros($unit) ?></span>
                    </div>
                  </div>
                  <div class="col-12 col-lg-2 text-center">
                    <div class="quantity-selector" data-index="<?= (int)$idx ?>">
                      <button class="quantity-btn decrease" type="button" aria-label="Diminuer" data-index="<?= (int)$idx ?>">
                        <i class="bi bi-dash"></i>
                      </button>
                      <input
                        type="number"
                        class="quantity-input"
                        value="<?= $qty ?>"
                        min="1"
                        data-index="<?= (int)$idx ?>"
                      >
                      <button class="quantity-btn increase" type="button" aria-label="Augmenter" data-index="<?= (int)$idx ?>">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>
                  </div>
                  <div class="col-12 col-lg-2 text-center mt-3 mt-lg-0">
                    <div class="item-total" data-index="<?= (int)$idx ?>">
                      <span><?= $euros($total) ?></span>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End Cart Item -->
            <?php endforeach; ?>
            <div class="cart-actions">
              <div class="row">
                <div class="col-lg-12 col-md-12 text-md-end">
                  <a href="/cart/clear" class="btn btn-outline-danger">
                    <i class="bi bi-trash"></i> Tout effacer
                  </a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <!-- Résumé -->
      <div class="col-lg-4 aos-init aos-animate" data-aos="fade-up" data-aos-delay="300">
        <div class="cart-summary">
          <h4 class="summary-title">Résumé de la commande</h4>
          <div class="summary-item">
            <span class="summary-label">Sous-total</span>
            <span class="summary-value"><?= $euros($grand) ?></span>
          </div>
          <div class="summary-total">
            <span class="summary-label">Total</span>
            <span class="summary-value"><?= $euros($grand) ?></span>
          </div>
          <div class="checkout-button">
            <a href="#" class="btn btn-accent w-100">
              Passer au paiement <i class="bi bi-arrow-right"></i>
            </a>
          </div>
          <div class="continue-shopping">
            <a href="/pizzas" class="btn btn-link w-100">
              <i class="bi bi-arrow-left"></i> Continuer les achats
            </a>
          </div>
          <div class="payment-methods">
            <p class="payment-title">Nous acceptons</p>
            <div class="payment-icons">
              <i class="bi bi-credit-card-2-front"></i>
              <i class="bi bi-paypal"></i>
              <i class="bi bi-wallet2"></i>
              <i class="bi bi-apple"></i>
              <i class="bi bi-google"></i>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /row -->
  </div><!-- /container -->
</section>

<!-- Modale Bootstrap (aucun CSS custom ajouté) -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form class="modal-content" id="editItemForm">
      <div class="modal-header">
        <h5 class="modal-title">
          Modifier la pizza — <span id="em-name"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="em-index" name="index" value="">
        <input type="hidden" id="em-pizza-id" value="">
        <div class="row g-3">
          <div class="col-md-5">
            <img id="em-photo" src="/assets/img/restaurant/default.webp" class="img-fluid" alt="">
          </div>
          <div class="col-md-7">
            <div class="mb-3">
              <label class="form-label">Taille</label>
              <div id="em-sizes"></div>
            </div>
            <div class="mb-3">
              <label for="em-extras" class="form-label">Ingrédients additionnels</label>
              <select id="em-extras" name="extras[]" class="form-select" multiple size="6"></select>
              <div class="form-text">On peut ajouter, retirer ou sélectionner plusieurs ingrédients en <kbd>cliquant</kbd> dessus.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <div class="me-auto small text-muted" id="em-hint">La quantité se change depuis la ligne du panier.</div>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>