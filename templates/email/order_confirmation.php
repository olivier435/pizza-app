<?php
/** @var \App\Entity\Purchase $purchase */
/** @var array $user */
/** @var callable $fmt (facultatif si tu le passes dans le context) */

$items = $purchase->getItems();
$fmtLocal = $fmt ?? fn(int $cents) => number_format($cents / 100, 2, ',', ' ') . ' €';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de votre commande <?= htmlspecialchars($purchase->getNumber()) ?></title>
</head>
<body>
    <h1>Merci pour votre commande 🍕</h1>

    <p>Bonjour <?= htmlspecialchars($user['firstname'] ?? 'cher client') ?>,</p>

    <p>
        Nous avons bien reçu votre commande
        <strong>#<?= htmlspecialchars($purchase->getNumber()) ?></strong>
        du <?= $purchase->getCreatedAt()?->format('d/m/Y H:i') ?>.
    </p>

    <h2>Récapitulatif</h2>
    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse;">
        <thead>
            <tr>
                <th>Pizza</th>
                <th>Taille</th>
                <th>Qté</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it->getPizzaName() ?? 'Pizza') ?></td>
                <td><?= htmlspecialchars($it->getSizeLabel() ?? '—') ?></td>
                <td align="center"><?= (int)$it->getQty() ?></td>
                <td align="right"><?= $fmtLocal($it->getUnitPriceCents()) ?></td>
                <td align="right"><?= $fmtLocal($it->getLineTotalCents()) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" align="right">Total</th>
                <th align="right"><?= $fmtLocal($purchase->getTotalCents()) ?></th>
            </tr>
        </tfoot>
    </table>

    <p>
        Vous pourrez retrouver ce récapitulatif dans votre espace
        <a href="http://localhost:8000/compte?tab=orders">Mon compte</a>.
    </p>

    <p>À très bientôt,<br>L'équipe Le Papacionu</p>
</body>
</html>