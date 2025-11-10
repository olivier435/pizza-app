<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;
use App\Repository\PizzaRepository;
use App\Repository\IngredientRepository;
use App\Repository\SizeRepository;

final class PizzaController extends Controller
{
    public function index(): void
    {
        $repo = new PizzaRepository;

        // 2 pizzas recommandées
        $recommended = $repo->findRecommended(2);

        // toutes les pizzas actives
        $all = $repo->findAll();

        $pageTitle = 'Nos Pizzas - Back Pizza';

        $this->render('pizza/index', [
            'pageTitle'   => $pageTitle,
            'recommended' => $recommended,
            'pizzas'      => $all,
        ]);
    }

    public function showJson(array $params): void
    {
        try {
            header('Content-Type: application/json; charset=utf-8');

            $id = isset($params['id']) ? (int)$params['id'] : 0;
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid id']);
                return;
            }

            $pizzaRepo = new \App\Repository\PizzaRepository();
            $pizza = $pizzaRepo->findById($id);
            if (!$pizza) {
                http_response_code(404);
                echo json_encode(['error' => 'Not found']);
                return;
            }

            $sizeRepo = new SizeRepository;
            $sizeRows = $sizeRepo->findAllLabelDiameter();
            $diameters = [];
            foreach ($sizeRows as $r) {
                $diameters[$r['label']] = $r['diameterCm'];
            }
            $base = $pizza->getBasePriceCents();
            $sizes = [
                ['label' => 'M',  'priceCents' => max(0, $base - 300), 'diameterCm' => $diameters['M'] ?? '28.0'],
                ['label' => 'L',  'priceCents' => $base,               'diameterCm' => $diameters['L'] ?? '33.0'],
                ['label' => 'XL', 'priceCents' => $base + 300,         'diameterCm' => $diameters['XL'] ?? '40.0'],
            ];

            // ⚠️ Requiert la colonne extraPriceCents
            $ingRepo = new IngredientRepository();
            $extras = $ingRepo->findAllExtrasActive();

            echo json_encode([
                'id'             => $pizza->getId(),
                'name'           => $pizza->getName(),
                'photoUrl'       => $pizza->getPhotoUrl(),
                'description'    => $pizza->getDescription(),
                'basePriceCents' => $base,
                'sizes'          => $sizes,
                'extras'         => $extras,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'error'   => 'Internal error',
                'message' => APP_ENV === 'dev' ? $e->getMessage() : 'Please try later',
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}