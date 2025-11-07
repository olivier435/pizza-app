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