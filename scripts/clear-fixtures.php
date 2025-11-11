<?php

declare(strict_types=1);

use App\Repository\UserRepository;

require_once __DIR__ . '/../config/bootstrap.php';

// Interdit en HTTP : uniquement CLI
// php scripts/lclear-fixtures.php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "403 Forbidden - Script CLI uniquement.\n";
    exit;
}

echo "=== Suppression des comptes fixtures ===\n";

$repo = new UserRepository();
$emails = ['admin@gmail.com', 'user0@gmail.com', 'user1@gmail.com'];

foreach ($emails as $email) {
    $user = $repo->findByEmail($email);
    if (!$user) {
        echo "ℹ️  $email non trouvé, ignoré.\n";
        continue;
    }
    $deleted = $repo->deleteByEmail($email);
    echo $deleted ? "🗑️  $email supprimé.\n" : "❌ Erreur suppression $email\n";
}

echo "=== Terminé ===\n";