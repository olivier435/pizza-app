<?php
$flashes = [];

if (!empty($_SESSION['_flash']) && is_array($_SESSION['_flash'])) {
    foreach ($_SESSION['_flash'] as $item) {
        if (is_string($item)) {
            $flashes[] = ['type' => 'info', 'msg' => $item];
        } elseif (is_array($item)) {
            $type = $item['type'] ?? 'info';
            $msg  = $item['msg']  ?? '';
            if ($msg !== '') {
                $flashes[] = ['type' => $type, 'msg' => $msg];
            }
        }
    }
    unset($_SESSION['_flash']);
}

// Ancien format : simple chaîne
if (!empty($_SESSION['flash']) && is_string($_SESSION['flash'])) {
    $flashes[] = ['type' => 'info', 'msg' => $_SESSION['flash']];
    unset($_SESSION['flash']);
}

// Mapping minimal vers les classes Bootstrap
$map = [
    'success' => 'success',
    'info'    => 'info',
    'warning' => 'warning',
    'danger'  => 'danger',
    'error'   => 'danger', // tolère "error" → "danger"
];

// Rendu
if (!empty($flashes)): ?>
    <?php foreach ($flashes as $f):
      $type = strtolower((string)$f['type']);
      $cls  = $map[$type] ?? 'info';
      $msg  = (string)$f['msg'];
      if ($msg === '') continue;
    ?>
      <div class="alert alert-<?= htmlspecialchars($cls) ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endforeach; ?>
<?php endif; ?>