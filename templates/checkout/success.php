<section class="inner-hero section dark-background"></section>
<div class="page-auth light-background">
    <div class="container d-lg-flex justify-content-between align-items-center">
        <h1 class="mb-2 mb-lg-0">Commande</h1>
        <nav class="breadcrumbs">
            <ol>
                <li><a href="/">Accueil</a></li>
                <li class="current">Commande</li>
            </ol>
        </nav>
    </div>
</div>
<section id="checkout-success" class="checkout section">
    <?php include __DIR__ . '/../layout/_flash.php'; ?>
    <div class="container aos-init aos-animate" data-aos="fade-up" data-aos-delay="100">

        <div class="text-center mb-5">
            <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            <h2 class="mt-3">Merci pour votre commande !</h2>
            <p class="lead">Votre commande <strong><?= htmlspecialchars($purchase->getNumber()) ?></strong> a bien été enregistrée.</p>
            <p>Total payé : <strong><?= htmlspecialchars($total) ?></strong></p>
        </div>

        <div class="card shadow-sm p-4">
            <h4>Détails de la commande</h4>
            <ul class="list-group list-group-flush">
                <?php foreach ($purchase->getItems() as $item): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($item->getPizzaName() ?? 'Pizza') ?></strong>
                            <small class="d-block text-muted">
                                Taille : <?= htmlspecialchars($item->getSizeLabel() ?? '—') ?>
                            </small>
                        </div>
                        <span>
                            <?= (int)$item->getQty() ?> ×
                            <?= number_format($item->getUnitPriceCents() / 100, 2, ',', ' ') ?> €
                            = <strong><?= number_format($item->getLineTotalCents() / 100, 2, ',', ' ') ?> €</strong>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="text-center mt-5">
            <a href="/" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Retour à l'accueil</a>
            <a href="/compte?tab=orders" class="btn btn-primary ms-2"><i class="bi bi-box-seam"></i> Mes commandes</a>
        </div>

    </div>
</section>