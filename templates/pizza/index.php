    <section class="inner-hero section dark-background">
    </section>
    <section id="menu" class="menu section">
        <div class="container section-title" data-aos="fade-up">
            <span class="description-title">Menu</span>
            <h2>Nos Pizzas</h2>
            <p>Dégustez nos créations artisanales, élaborées avec des produits d'exception et cuites à la perfection.</p>
        </div>
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <?php if (!empty($recommended)): ?>
                <div class="chef-recommendations" data-aos="fade-up" data-aos-delay="200">
                    <div class="section-header">
                        <h3><i class="bi bi-star-fill"></i> Recommandations du Chef</h3>
                        <p>Nos coups de cœur du moment, soigneusement sélectionnés par le Chef.</p>
                    </div>
                    <div class="row g-4">
                        <?php foreach ($recommended as $pizza): ?>
                            <div class="col-lg-6">
                                <div class="recommendation-card">
                                    <div class="recommendation-image">
                                        <img
                                            src="<?= htmlspecialchars($pizza->getPhotoUrl() ?? '/assets/img/restaurant/default.webp') ?>"
                                            alt="<?= htmlspecialchars($pizza->getName()) ?>"
                                            class="img-fluid">
                                        <div class="chef-badge">
                                            <i class="bi bi-award"></i>
                                            <span>Choix du chef</span>
                                        </div>
                                    </div>
                                    <div class="recommendation-content">
                                        <div class="recommendation-header">
                                            <h4><?= htmlspecialchars($pizza->getName()) ?></h4>
                                            <div class="recommendation-price"><?= $pizza->getBasePriceEuros() ?> €</div>
                                        </div>
                                        <p><?= htmlspecialchars($pizza->getDescription() ?? 'Une création originale du chef, à base d\'ingrédients italiens soigneusement sélectionnés.') ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-center text-muted my-5">Aucune recommandation du chef n'est disponible pour le moment.</p>
            <?php endif; ?>
            <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
                <div class="menu-filters isotope-filters mt-5 mb-5" data-aos="fade-up" data-aos-delay="200">
                    <ul>
                        <li data-filter="*" class="filter-active">Toutes</li>
                        <li data-filter=".filter-classic">Classiques</li>
                        <li data-filter=".filter-special">Spéciales</li>
                        <li data-filter=".filter-vegetarian">Végétariennes</li>
                    </ul>
                </div>
                <div class="menu-grid isotope-container row gy-5" data-aos="fade-up" data-aos-delay="300">
                    <?php foreach ($pizzas as $pizza): ?>
                        <div class="col-xl-4 col-lg-6 isotope-item <?= htmlspecialchars($pizza->getFilter()) ?>">
                            <div class="menu-card <?= $pizza->isRecommended() ? 'featured' : '' ?>">
                                <div class="menu-card-image">
                                    <img
                                        src="<?= htmlspecialchars($pizza->getPhotoUrl() ?? '/assets/img/restaurant/default.webp') ?>"
                                        alt="<?= htmlspecialchars($pizza->getName()) ?>"
                                        class="img-fluid">
                                    <!-- bouton plein cadre -->
                                    <button class="stretched-link btn btn-link p-0 border-0 open-pizza-modal"
                                        data-pizza-id="<?= (int)$pizza->getId() ?>"
                                        data-pizza-name="<?= htmlspecialchars($pizza->getName(), ENT_QUOTES) ?>"
                                        aria-label="Configurer la pizza <?= htmlspecialchars($pizza->getName()) ?>">
                                    </button>
                                    <div class="dietary-badges">
                                        <?php if ($pizza->getFilter() === 'filter-vegetarian'): ?>
                                            <span class="badge-vegetarian" title="Végétarienne"><i class="bi bi-leaf"></i></span>
                                        <?php elseif ($pizza->getFilter() === 'filter-special'): ?>
                                            <span class="badge-special" title="Spéciale du chef"><i class="bi bi-fire"></i></span>
                                        <?php else: ?>
                                            <span class="badge-classic" title="Classique"><i class="bi bi-star"></i></span>
                                        <?php endif; ?>

                                        <?php if ($pizza->isRecommended()): ?>
                                            <span class="badge-chef" title="Recommandée par le chef"><i class="bi bi-award"></i></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="price-overlay"><?= $pizza->getBasePriceEuros() ?> €</div>
                                </div>
                                <div class="menu-card-content">
                                    <h4><?= htmlspecialchars($pizza->getName()) ?></h4>
                                    <p><?= htmlspecialchars($pizza->getDescription() ?? '') ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="pizzaModal" tabindex="-1" aria-labelledby="pizzaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="pizzaModalForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pizzaModalLabel">Configurer votre pizza</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <img id="pm-photo" src="" alt="" class="img-fluid rounded">
                            </div>
                            <div class="col-md-7">
                                <h4 id="pm-name" class="mb-1"></h4>
                                <p id="pm-desc" class="text-muted small"></p>

                                <input type="hidden" name="pizzaId" id="pm-id" value="">

                                <!-- Taille -->
                                <div class="mb-3">
                                    <label class="form-label">Taille</label>
                                    <div id="pm-sizes" class="d-flex gap-2">
                                        <!-- radios injectées via JS -->
                                    </div>
                                </div>

                                <!-- Quantité -->
                                <div class="mb-3">
                                    <label for="pm-qty" class="form-label">Quantité</label>
                                    <div class="input-group" style="max-width:180px;">
                                        <button class="btn btn-outline-secondary" type="button" id="pm-qty-minus">−</button>
                                        <input type="text" class="form-control text-center" name="qty" id="pm-qty" value="1" min="1">
                                        <button class="btn btn-outline-secondary" type="button" id="pm-qty-plus">+</button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="pm-extras" class="form-label">Ingrédients additionnels</label>
                                    <select id="pm-extras" name="extras[]" class="form-select" multiple size="6">
                                        <!-- options injectées via JS -->
                                    </select>
                                    <div class="form-text">Maintenez <kbd>Ctrl</kbd> ou <kbd>Cmd</kbd> pour une sélection multiple.</div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="fs-5">
                                        Total : <strong id="pm-total">0,00 €</strong>
                                    </div>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bi bi-cart-plus"></i> Ajouter au panier
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>