<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PizzaRepository;
use App\Repository\IngredientRepository;

/**
 * Contrôleur du panier
 */
final class CartController extends Controller
{
    /**
     * GET /panier — Affichage du panier
     */
    public function show(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $cart = $_SESSION['cart'] ?? [];
        $totalCents = 0;

        foreach ($cart as $line) {
            $totalCents += (int)($line['totalCents'] ?? 0);
        }

        $this->render('cart/show', [
            'cart'       => $cart,
            'totalEuros' => number_format($totalCents / 100, 2, ',', ' ')
        ]);
    }

    /**
     * POST /cart/add — Ajout d'une pizza au panier (AJAX)
     * Attend: pizzaId, size (M|L|XL), qty, extras[] (IDs)
     * Renvoie JSON: { ok: bool, count: int, redirect: "/panier" }
     */
    public function add(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $data    = $_POST;
        $pizzaId = (int)($data['pizzaId'] ?? 0);
        $size    = strtoupper(trim((string)($data['size'] ?? 'L')));
        $qty     = max(1, (int)($data['qty'] ?? 1));
        $extras  = $data['extras'] ?? [];

        if ($pizzaId <= 0 || !in_array($size, ['M', 'L', 'XL'], true)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Bad payload']);
            return;
        }

        $pizzaRepo = new PizzaRepository();
        $pizza = $pizzaRepo->findById($pizzaId);
        if (!$pizza) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Pizza not found']);
            return;
        }

        $unitCents = (int)$pizza->getPriceForSize($size);

        $extrasIds = array_values(array_filter(array_map('intval', (array)$extras)));
        $extrasList = [];
        $extraTotalCents = 0;

        if (!empty($extrasIds)) {
            $ingRepo = new IngredientRepository();
            $avail = $ingRepo->findAllExtrasActive(); // id, name, unit, extraPriceCents
            $byId = [];
            foreach ($avail as $e) {
                $byId[(int)$e['id']] = [
                    'id'    => (int)$e['id'],
                    'name'  => (string)$e['name'],
                    'price' => (int)$e['extraPriceCents'],
                ];
            }
            foreach ($extrasIds as $eid) {
                if (isset($byId[$eid])) {
                    $extrasList[] = $byId[$eid];
                    $extraTotalCents += $byId[$eid]['price'];
                }
            }
        }

        $lineUnitCents  = $unitCents + $extraTotalCents;
        $lineTotalCents = $lineUnitCents * $qty;

        $_SESSION['cart'] ??= [];
        $_SESSION['cart'][] = [
            'pizzaId'    => $pizza->getId(),
            'name'       => $pizza->getName(),
            'size'       => $size,
            'qty'        => $qty,
            'unitCents'  => $lineUnitCents,
            'totalCents' => $lineTotalCents,
            'extras'     => $extrasList,
            'photo'      => $pizza->getPhotoUrl(),
        ];

        $distinct = [];
        foreach ($_SESSION['cart'] as $l) {
            $pid = $l['pizzaId'] ?? null;
            if ($pid !== null) $distinct[(int)$pid] = true;
        }

        echo json_encode([
            'ok'       => true,
            'count'    => count($distinct),
            'redirect' => '/pizzas'
        ]);
    }

    /**
     * GET /cart/count — Compteur distinct pour le badge (AJAX)
     */
    public function count(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $distinct = [];
        foreach (($_SESSION['cart'] ?? []) as $line) {
            $pid = $line['pizzaId'] ?? ($line['pizza_id'] ?? null);
            if ($pid !== null) $distinct[(int)$pid] = true;
        }

        echo json_encode(['count' => count($distinct)]);
    }

    /**
     * GET /cart/clear — Vide le panier
     */
    public function clear(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION['cart']);
        $this->redirect('/panier');
    }

    public function update(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        $qty   = isset($_POST['qty'])   ? (int)$_POST['qty']   : 1;
        $qty   = max(1, $qty);

        if (!isset($_SESSION['cart'][$index])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Line not found']);
            return;
        }

        $line = &$_SESSION['cart'][$index];
        $line['qty']        = $qty;
        $line['totalCents'] = (int)$line['unitCents'] * $qty;

        $grand = 0;
        $distinct = [];
        foreach ($_SESSION['cart'] as $l) {
            $grand += (int)$l['totalCents'];
            if (isset($l['pizzaId'])) $distinct[(int)$l['pizzaId']] = true;
        }

        $fmt = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

        echo json_encode([
            'ok'             => true,
            'qty'            => $line['qty'],
            'unitCents'      => (int)$line['unitCents'],
            'lineTotalCents' => (int)$line['totalCents'],
            'lineTotalEuros' => $fmt((int)$line['totalCents']),
            'grandCents'     => $grand,
            'grandEuros'     => $fmt($grand),
            'count'          => count($distinct),
        ]);
    }

    public function remove(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

        if (!isset($_SESSION['cart'][$index])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Line not found']);
            return;
        }

        array_splice($_SESSION['cart'], $index, 1);

        $grand = 0;
        $distinct = [];
        foreach ($_SESSION['cart'] as $l) {
            $grand += (int)$l['totalCents'];
            if (isset($l['pizzaId'])) $distinct[(int)$l['pizzaId']] = true;
        }

        $fmt = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

        echo json_encode([
            'ok'         => true,
            'grandCents' => $grand,
            'grandEuros' => $fmt($grand),
            'count'      => count($distinct),
            'empty'      => empty($_SESSION['cart']),
        ]);
    }

    /**
     * POST /cart/edit — Modifier taille/ingrédients d'une ligne, SANS changer la quantité.
     * Attend: index (ligne), size (M|L|XL) optionnel, extras[] optionnel.
     * - Si size absent → conserve la taille actuelle.
     * - Si extras absent → conserve les extras actuels.
     */
    public function edit(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $index  = isset($_POST['index']) ? (int)$_POST['index'] : -1;
        if (!isset($_SESSION['cart'][$index])) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Line not found']);
            return;
        }

        $line = $_SESSION['cart'][$index];

        $sizeIn   = isset($_POST['size']) ? strtoupper(trim((string)$_POST['size'])) : '';
        $size     = in_array($sizeIn, ['M','L','XL'], true) ? $sizeIn : (string)($line['size'] ?? 'L');

        $pizzaId = (int)($line['pizzaId'] ?? 0);
        $pizzaRepo = new PizzaRepository();
        $pizza = $pizzaRepo->findById($pizzaId);
        if (!$pizza) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Pizza not found']);
            return;
        }

        $unitBase = (int)$pizza->getPriceForSize($size);

        $extrasParamProvided = array_key_exists('extras', $_POST);
        $extrasIds = $extrasParamProvided
            ? array_values(array_filter(array_map('intval', (array)($_POST['extras'] ?? []))))
            : array_map(fn($e) => (int)($e['id'] ?? 0), (array)($line['extras'] ?? []));

        $extrasList = [];
        $extraTotalCents = 0;

        if (!empty($extrasIds)) {
            $ingRepo = new IngredientRepository();
            $avail = $ingRepo->findAllExtrasActive();
            $byId = [];
            foreach ($avail as $e) {
                $byId[(int)$e['id']] = [
                    'id'    => (int)$e['id'],
                    'name'  => (string)$e['name'],
                    'price' => (int)$e['extraPriceCents'],
                ];
            }
            foreach ($extrasIds as $eid) {
                if (isset($byId[$eid])) {
                    $extrasList[] = $byId[$eid];
                    $extraTotalCents += $byId[$eid]['price'];
                }
            }
        }

        $newUnit = $unitBase + $extraTotalCents;
        $qty     = max(1, (int)($line['qty'] ?? 1));

        $line['size']       = $size;
        $line['extras']     = $extrasList;
        $line['unitCents']  = $newUnit;
        $line['totalCents'] = $newUnit * $qty;

        $_SESSION['cart'][$index] = $line;

        $grand = 0;
        $distinct = [];
        foreach ($_SESSION['cart'] as $l) {
            $grand += (int)$l['totalCents'];
            if (isset($l['pizzaId'])) $distinct[(int)$l['pizzaId']] = true;
        }

        $fmt   = fn(int $c) => number_format($c / 100, 2, ',', ' ') . ' €';

        $extrasText = '';
        if (!empty($line['extras'])) {
            $names = array_map(fn($e) => (string)$e['name'], $line['extras']);
            $extrasText = implode(', ', $names);
        }

        echo json_encode([
            'ok'             => true,
            'size'           => $line['size'],
            'extrasText'     => $extrasText,
            'unitCents'      => (int)$line['unitCents'],
            'unitEuros'      => $fmt((int)$line['unitCents']),
            'lineTotalCents' => (int)$line['totalCents'],
            'lineTotalEuros' => $fmt((int)$line['totalCents']),
            'grandCents'     => $grand,
            'grandEuros'     => $fmt($grand),
            'count'          => count($distinct),
        ]);
    }
}