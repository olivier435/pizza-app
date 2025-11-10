<?php
declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PizzaRepository;
use App\Repository\IngredientRepository;

final class CartController extends Controller
{
    public function show(): void
    {
        $cart = $_SESSION['cart'] ?? [];
        $this->render('cart/show', [
            'cart' => $cart
        ]);
    }

    public function add(): void
    {
        // JSON ou form-encoded
        $data = $_POST;
        // champs attendus
        $pizzaId = (int)($data['pizzaId'] ?? 0);
        $size    = strtoupper(trim((string)($data['size'] ?? 'L')));
        $qty     = (int)($data['qty'] ?? 1);
        $extras  = $data['extras'] ?? []; // array d'IDs

        // Validation minimale
        if ($pizzaId <= 0 || $qty < 1 || !in_array($size, ['M','L','XL'], true)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Bad payload']);
            return;
        }

        $repo = new PizzaRepository();
        $pizza = $repo->findById($pizzaId);
        if (!$pizza) {
            http_response_code(404);
            echo json_encode(['ok' => false, 'error' => 'Pizza not found']);
            return;
        }

        // prix unitaire selon taille
        $unitCents = $pizza->getPriceForSize($size);

        // surcharge extra (on ne refait pas une requête, le front nous a donné les IDs ; 
        // en prod, on sécuriserait en recalculant côté serveur à partir de l'IngredientRepository)
        $extrasIds = array_values(array_filter(array_map('intval', (array)$extras)));
        $extrasList = []; // pour info panier
        $extraTotalCents = 0;

        if (!empty($extrasIds)) {
            // recalcul sécurisé (recommandé)
            $ingRepo = new IngredientRepository();
            $avail = $ingRepo->findAllExtrasActive();
            $byId = [];
            foreach ($avail as $e) { $byId[$e['id']] = $e; }

            foreach ($extrasIds as $eid) {
                if (isset($byId[$eid])) {
                    $extrasList[] = [
                        'id'    => $eid,
                        'name'  => $byId[$eid]['name'],
                        'price' => (int)$byId[$eid]['extraPriceCents'],
                    ];
                    $extraTotalCents += (int)$byId[$eid]['extraPriceCents'];
                }
            }
        }

        $lineUnitCents = $unitCents + $extraTotalCents; // prix unitaire + extras
        $lineTotalCents = $lineUnitCents * $qty;

        // Ajout en session
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];
        $_SESSION['cart'][] = [
            'pizzaId'   => $pizza->getId(),
            'name'      => $pizza->getName(),
            'size'      => $size,
            'qty'       => $qty,
            'unitCents' => $lineUnitCents,
            'totalCents'=> $lineTotalCents,
            'extras'    => $extrasList
        ];

        // Réponse JSON (AJAX)
        if (
            isset($_SERVER['HTTP_ACCEPT']) &&
            str_contains(strtolower($_SERVER['HTTP_ACCEPT']), 'application/json')
        ) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'redirect' => '/panier']);
            return;
        }

        // Fallback non-AJAX
        $this->redirect('/panier');
    }
}