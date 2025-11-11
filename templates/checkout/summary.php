<?php
$fmt = fn(int $c) => number_format($c/100, 2, ',', ' ') . ' €';
$total = 0;
foreach (($cart ?? []) as $l) $total += (int)($l['totalCents'] ?? 0);
?>
<section class="inner-hero section dark-background"></section>
<section class="section">
  <div class="container">
    <h2 class="mb-3">Récapitulatif</h2>
    <p>Bonjour <?= htmlspecialchars($user['firstname'] ?? '') ?>, voici votre commande :</p>
    <ul class="list-group mb-3">
      <?php foreach ($cart as $l): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <span><?= htmlspecialchars($l['name']) ?> (<?= htmlspecialchars($l['size']) ?>) × <?= (int)$l['qty'] ?></span>
          <strong><?= $fmt((int)$l['totalCents']) ?></strong>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="text-end mb-4"><h4>Total : <?= $fmt($total) ?></h4></div>
    <a href="/pizzas" class="btn btn-outline-secondary">Retour</a>
    <a href="/payment" class="btn btn-primary">Procéder au paiement</a>
  </div>
</section>