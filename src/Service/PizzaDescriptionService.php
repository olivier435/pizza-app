<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Ingredient;

final class PizzaDescriptionService
{
    /**
     * @param Ingredient[] $ingredients
     */
    public function buildDescriptionFromIngredients(array $ingredients): string
    {
        if (empty($ingredients)) {
            return '';
        }

        $names = [];
        foreach ($ingredients as $ingredient) {
            $label = trim((string)$ingredient->getName());
            if ($label !== '') {
                $names[] = $label;
            }
        }

        if (empty($names)) {
            return '';
        }

        // --- 🟦 1. REORDER : mettre toutes les sauces tomates au début ---
        $saucesTomates = [];
        $autres        = [];

        foreach ($names as $name) {
            // insensible à la casse
            if (mb_stripos($name, 'sauce tomate', 0, 'UTF-8') !== false) {
                $saucesTomates[] = $name;
            } else {
                $autres[] = $name;
            }
        }

        // Fusion : sauces d'abord, puis le reste
        $names = array_merge($saucesTomates, $autres);

        // --- 🟩 2. Construction de la phrase ---
        $count = count($names);

        if ($count === 1) {
            $sentence = sprintf('%s.', $names[0]);
        } elseif ($count === 2) {
            $sentence = sprintf('%s et %s.', $names[0], $names[1]);
        } else {
            $last   = array_pop($names);
            $firsts = implode(', ', $names);
            $sentence = sprintf('%s et %s.', $firsts, $last);
        }

        // Majuscule initiale
        $sentence = trim($sentence);
        $firstChar = mb_substr($sentence, 0, 1, 'UTF-8');
        $rest      = mb_substr($sentence, 1, null, 'UTF-8');

        return mb_strtoupper($firstChar, 'UTF-8') . $rest;
    }
}