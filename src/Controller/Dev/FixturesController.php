<?php

declare(strict_types=1);

namespace App\Controller\Dev;

use App\Core\Controller;
use App\Repository\UserRepository;

final class FixturesController extends Controller
{
    private function getEnvVar(string $key): ?string
    {
        $envLocal = __DIR__ . '/../../../.env.local';
        $envMain  = __DIR__ . '/../../../.env';
        $files = file_exists($envLocal) ? [$envLocal, $envMain] : [$envMain];

        foreach ($files as $path) {
            if (!file_exists($path)) continue;
            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                [$k, $v] = array_map('trim', explode('=', $line, 2) + [null, null]);
                if ($k === $key && $v !== '' && $v !== '__CHANGE_ME__') {
                    return $v;
                }
            }
        }
        return null;
    }

    private function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    private function checkAccess(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $isCli   = $this->isCli();
        $isAdmin = (($_SESSION['user']['role'] ?? null) === 'ADMIN');
        $expected = $this->getEnvVar('FIXTURES_SECRET') ?? '';
        $given    = $_GET['secret'] ?? '';

        // Autorisations :
        // - CLI
        // - ADMIN connecté
        // - Secret valide (obligatoire en HTTP si pas ADMIN)
        $allowed = $isCli || $isAdmin || ($expected !== '' && hash_equals($expected, (string)$given));

        if (!$allowed) {
            http_response_code(403);
            if ($isCli) {
                fwrite(STDERR, "403 Forbidden - Secret requis ou ADMIN.\n");
            } else {
                $this->render('dev/fixtures', [
                    'title'    => 'Accès refusé',
                    'subtitle' => '403 — Secret requis ou ADMIN.',
                    'logs'     => [['type' => 'error', 'msg' => 'Accès interdit']],
                ]);
            }
            exit;
        }

        // (Optionnel) restreindre à APP_ENV=dev :
        $appEnv = $this->getEnvVar('APP_ENV') ?? 'dev';
        if ($appEnv !== 'dev' && !$isAdmin) {
            http_response_code(403);
            if ($isCli) {
                fwrite(STDERR, "403 Forbidden - Fixtures désactivées hors DEV.\n");
            } else {
                $this->render('dev/fixtures', [
                    'title'    => 'Accès refusé',
                    'subtitle' => '403 — Fixtures désactivées hors DEV.',
                    'logs'     => [['type' => 'error', 'msg' => 'Hors environnement DEV']],
                ]);
            }
            exit;
        }
    }

    public function load(): void
    {
        $this->checkAccess();

        $repo = new UserRepository();
        $logs = [];

        $add = function (string $type, string $msg) use (&$logs): void {
            $logs[] = ['type' => $type, 'msg' => $msg];
        };

        $create = function (string $email, string $firstname, string $lastname, string $role = 'USER', ?array $extra = null) use ($repo, $add): void {
            $plain = 'password';
            $hash  = password_hash($plain, PASSWORD_DEFAULT);
            if ($repo->findByEmail($email)) {
                $add('info', "$email existe déjà, ignoré.");
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

            $id ? $add('ok', "$email créé (rôle=$role, mdp=password)")
                : $add('error', "Erreur création $email");
        };

        $create('admin@gmail.com', 'Admin', 'Admin', 'ADMIN');
        $create('user0@gmail.com', 'Alice', 'Leclerc');
        $create('user1@gmail.com', 'Jean', 'Derieux');

        if ($this->isCli()) {
            echo "=== Chargement des fixtures ===\n";
            foreach ($logs as $l) echo strtoupper($l['type']) . ': ' . $l['msg'] . "\n";
            echo "=== Terminé ===\n";
        } else {
            $this->render('dev/fixtures', [
                'title'    => 'Fixtures — Chargement terminé',
                'subtitle' => '3 comptes créés : 1 ADMIN + 2 USER (mdp : password)',
                'logs'     => $logs,
            ]);
        }
    }

    public function clear(): void
    {
        $this->checkAccess();

        $repo = new UserRepository();
        $logs = [];
        $add = function (string $type, string $msg) use (&$logs): void {
            $logs[] = ['type' => $type, 'msg' => $msg];
        };
        foreach (['admin@gmail.com', 'user0@gmail.com', 'user1@gmail.com'] as $email) {
            $u = $repo->findByEmail($email);
            if (!$u) {
                $add('info', "$email non trouvé, ignoré.");
                continue;
            }
            $ok = $repo->deleteByEmail($email);
            $ok ? $add('ok', "$email supprimé.") : $add('error', "Erreur suppression $email");
        }

        if ($this->isCli()) {
            echo "=== Suppression des fixtures ===\n";
            foreach ($logs as $l) echo strtoupper($l['type']) . ': ' . $l['msg'] . "\n";
            echo "=== Terminé ===\n";
        } else {
            $this->render('dev/fixtures', [
                'title'    => 'Fixtures — Suppression terminée',
                'subtitle' => 'Comptes de test supprimés',
                'logs'     => $logs,
            ]);
        }
    }
}