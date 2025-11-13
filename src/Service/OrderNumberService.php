<?php
declare(strict_types=1);

namespace App\Service;

final class OrderNumberService
{
    public function generateForId(int $id): string
    {
        // Exemple: ORD-2025-000123
        return 'ORD-' . date('Y') . '-' . str_pad((string)$id, 6, '0', STR_PAD_LEFT);
    }
}