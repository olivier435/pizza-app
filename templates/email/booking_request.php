<?php
/** @var array $data */

$name     = $data['name']     ?? '';
$email    = $data['email']    ?? '';
$phone    = $data['phone']    ?? '';
$dateRaw  = $data['date']     ?? '';
$timeRaw  = $data['time']     ?? '';
$people   = $data['people']   ?? '';
$occasion = $data['occasion'] ?? '';
$message  = $data['message']  ?? '';

// Formatage date (2025-11-15 → 15/11/2025)
$dateFormatted = $dateRaw;
try {
    if ($dateRaw !== '') {
        $dt = new \DateTimeImmutable($dateRaw);
        $dateFormatted = $dt->format('d/m/Y');
    }
} catch (\Throwable) {
    // on laisse tel quel si problème
}

// Formatage heure (20:00 → 20h00)
$timeFormatted = $timeRaw;
if (preg_match('/^(\d{2}):(\d{2})$/', $timeRaw, $m)) {
    $timeFormatted = $m[1] . 'h' . $m[2];
}

// Nombre de personnes (2 → "2 personnes")
$peopleLabel = $people;
if ($people !== '') {
    if ($people === '1') {
        $peopleLabel = '1 personne';
    } elseif ($people === '6') {
        $peopleLabel = '6 personnes ou +';
    } else {
        $peopleLabel = $people . ' personnes';
    }
}

// Occasion lisible
$occasionLabel = '';
switch ($occasion) {
    case 'birthday':
        $occasionLabel = 'Anniversaire';
        break;
    case 'anniversary':
        $occasionLabel = 'Fête';
        break;
    case 'business':
        $occasionLabel = 'Dîner professionnel';
        break;
    case 'date':
        $occasionLabel = 'Soirée en couple';
        break;
    case 'celebration':
        $occasionLabel = 'Célébration';
        break;
    case 'other':
        $occasionLabel = 'Autre';
        break;
    default:
        $occasionLabel = '';
        break;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle demande de réservation</title>
</head>
<body>
    <h1>Nouvelle demande de réservation</h1>

    <h2>Coordonnées du client</h2>
    <ul>
        <li><strong>Nom :</strong> <?= htmlspecialchars($name) ?></li>
        <li><strong>Email :</strong> <?= htmlspecialchars($email) ?></li>
        <li><strong>Téléphone :</strong> <?= htmlspecialchars($phone) ?></li>
    </ul>

    <h2>Détails de la réservation</h2>
    <ul>
        <li><strong>Date :</strong> <?= htmlspecialchars($dateFormatted) ?></li>
        <li><strong>Heure :</strong> <?= htmlspecialchars($timeFormatted) ?></li>
        <li><strong>Nombre de personnes :</strong> <?= htmlspecialchars($peopleLabel) ?></li>
        <?php if ($occasionLabel !== ''): ?>
            <li><strong>Occasion :</strong> <?= htmlspecialchars($occasionLabel) ?></li>
        <?php endif; ?>
    </ul>

    <?php if ($message !== ''): ?>
        <h2>Message</h2>
        <p><?= nl2br(htmlspecialchars($message)) ?></p>
    <?php endif; ?>
</body>
</html>