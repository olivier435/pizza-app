<?php
/** @var string $title */
/** @var array $logs */
/** @var string|null $subtitle */

$homeUrl  = '/';
$loadUrl  = '/_fixtures/load';
$clearUrl = '/_fixtures/clear';
?>
<section class="inner-hero section dark-background"></section>
<section class="section" style="padding:3rem 1rem 20rem;">
  <div class="container" style="max-width:900px;">
    <div class="card p-4" style="background-color:#0b1220;border:1px solid #1f2937;border-radius:12px;">
      <h2 style="margin-bottom:0.5rem;color: #ff0000;"><?= htmlspecialchars($title) ?></h2>
      <?php if (!empty($subtitle)): ?>
        <p style="color:#94a3b8;"><?= htmlspecialchars($subtitle) ?></p>
      <?php endif; ?>

      <ul style="list-style:none;padding:0;margin-top:1rem;">
        <?php foreach ($logs as $l): 
          $type = $l['type'] ?? 'info';
          $msg  = $l['msg']  ?? '';
          $color = $type === 'ok' ? '#16a34a' : ($type === 'error' ? '#ef4444' : '#60a5fa');
        ?>
          <li style="border:1px solid rgba(255,255,255,.1);border-left:4px solid <?= $color ?>;padding:10px 12px;margin-bottom:8px;border-radius:6px;background:rgba(255,255,255,.03);color: #FFFFFF;">
            <strong style="color:<?= $color ?>;text-transform:uppercase;"><?= htmlspecialchars($type) ?></strong> —
            <?= htmlspecialchars($msg) ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="mt-4 d-flex gap-2">
        <a href="<?= $homeUrl ?>" class="btn btn-outline-light">Accueil</a>
        <a href="<?= $loadUrl ?>" class="btn btn-primary">Charger fixtures</a>
        <a href="<?= $clearUrl ?>" class="btn btn-danger">Vider fixtures</a>
      </div>
      <p class="text-muted mt-3" style="color: #FFFFFF !important;">
        Astuce : ajoute <code>?secret=VOTRE_SECRET</code> si vous n'êtes pas connecté en ADMIN.
      </p>
    </div>
  </div>
</section>