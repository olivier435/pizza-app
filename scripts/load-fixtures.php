<?php
declare(strict_types=1);

use App\Repository\UserRepository;

require_once __DIR__ . '/../config/bootstrap.php';

// Interdit en HTTP : uniquement CLI
// php scripts/load-fixtures.php
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "403 Forbidden - Script CLI uniquement.\n";
    exit;
}

echo "=== Chargement des fixtures utilisateurs ===\n";

$repo = new UserRepository();

function createUser(UserRepository $repo, string $email, string $firstname, string $lastname, string $role = 'USER', ?array $extra = null): void 
{
    $plain = 'password';
    $hash  = password_hash($plain, PASSWORD_DEFAULT);
    if ($repo->findByEmail($email)) {
        echo "⏭️  $email existe déjà, ignoré.\n";
        return;
    }

    $id = $repo->createUser([
        'email'        => $email,
        'passwordHash' => $hash,
        'firstname'    => $firstname,
        'lastname'     => $lastname,
        'role'         => $role,
        'address'      => $extra['address']    ?? null,
        'postalCode'   => $extra['postalCode'] ?? null,
        'city'         => $extra['city']       ?? null,
        'phone'        => $extra['phone']      ?? null,
    ]);
    echo $id ? "✅ $email créé (id=$id, rôle=$role, mdp=password)\n" : "❌ Erreur création $email\n";
}

createUser($repo, 'admin@gmail.com', 'Admin', 'Admin', 'ADMIN');
createUser($repo, 'user0@gmail.com', 'Alice', 'Leclerc');
createUser($repo, 'user1@gmail.com', 'Jean', 'Derieux');

echo "=== Terminé ===\n";