<div class="tab-pane fade <?= $isOrders ? 'show active' : '' ?>" id="orders" role="tabpanel">
    <div class="section-header aos-init aos-animate" data-aos="fade-up">
        <h2>Mes commandes</h2>
    </div>
    <div class="orders-grid">
        <?php if (empty($orders)): ?>
            <div class="alert alert-info">Aucune commande pour le moment.</div>
        <?php else: ?>
            <?php foreach ($orders as $idx => $order): ?>
                <?php
                $statusCode  = strtoupper($order->getStatus());
                $statusClass = match ($statusCode) {
                    'PAID'      => 'paid',
                    'PENDING'   => 'pending',
                    'CANCELLED' => 'cancelled',
                    'DELIVERED' => 'delivered',
                    default     => 'pending',
                };
                $statusLabel = match ($statusCode) {
                    'PAID'      => 'Payée',
                    'PENDING'   => 'En cours',
                    'CANCELLED' => 'Annulée',
                    'DELIVERED' => 'Emportée',
                    default     => 'En cours',
                };
                $itemCount = 0;
                foreach ($order->getItems() as $it) {
                    $itemCount += (int)$it->getQty();
                }
                $dateStr    = $order->getCreatedAt() ? $order->getCreatedAt()->format('d/m/Y H:i') : '';
                $collapseId = 'details' . (int)$order->getId();
                $items      = $order->getItems();
                ?>
                <div class="order-card aos-init aos-animate" data-aos="fade-up" data-aos-delay="<?= 100 + $idx * 50 ?>">
                    <div class="order-header">
                        <div class="order-id">
                            <span class="label">Commande :</span>
                            <span class="value">#<?= htmlspecialchars($order->getNumber()) ?></span>
                        </div>
                        <div class="order-date"><?= htmlspecialchars($dateStr) ?></div>
                    </div>
                    <div class="order-content">
                        <div class="product-grid">
                            <?php foreach ($items as $it): ?>
                                <img
                                    src="<?= htmlspecialchars($it->getPizzaPhotoUrl()) ?>"
                                    alt="<?= htmlspecialchars($it->getPizzaName() ?? 'Pizza') ?>"
                                    loading="lazy">
                            <?php endforeach; ?>
                        </div>
                        <div class="order-info">
                            <div class="info-row">
                                <span>Status</span>
                                <span class="status <?= $statusClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                            </div>
                            <div class="info-row">
                                <span>Articles</span>
                                <span><?= $itemCount ?> <?= $itemCount > 1 ? 'pizzas' : 'pizza' ?></span>
                            </div>
                            <div class="info-row">
                                <span>Total</span>
                                <span class="price"><?= $fmt($order->getTotalCents()) ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <button type="button" class="btn-details collapsed" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId ?>" aria-expanded="false">Détails</button>
                    </div>
                    <!-- Détails -->
                    <div class="order-details collapse" id="<?= $collapseId ?>">
                        <div class="details-content">
                            <div class="detail-section">
                                <h5>Détails de la commande</h5>
                            </div>
                            <div class="detail-section">
                                <h5>Pizzas (<?= $itemCount ?>)</h5>
                                <div class="order-items">
                                    <?php foreach ($order->getItems() as $it): ?>
                                        <div class="item">
                                            <img
                                                src="<?= htmlspecialchars($it->getPizzaPhotoUrl()) ?>"
                                                alt="<?= htmlspecialchars($it->getPizzaName() ?? 'Pizza') ?>"
                                                loading="lazy">
                                            <div class="item-info">
                                                <h6><?= htmlspecialchars($it->getPizzaName() ?? 'Pizza') ?></h6>
                                                <div class="item-meta">
                                                    <span class="sku">Taille : <?= htmlspecialchars($it->getSizeLabel() ?? '—') ?></span>
                                                    <span class="qty ms-3">Qté : <?= (int)$it->getQty() ?></span>
                                                </div>
                                            </div>
                                            <div class="item-price">
                                                <?= $fmt($it->getLineTotalCents()) ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="detail-section">
                                <h5>Détails du prix</h5>
                                <div class="price-breakdown">
                                    <div class="price-row">
                                        <span>Sous total</span>
                                        <span><?= $fmt($order->getTotalCents()) ?></span>
                                    </div>
                                    <div class="price-row total">
                                        <span>Total</span>
                                        <span><?= $fmt($order->getTotalCents()) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>