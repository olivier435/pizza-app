<?php
namespace App\Repository;

use App\Core\DB;
use PDO;

final class SizeRepository
{
    private PDO $pdo;
    public function __construct() { $this->pdo = DB::pdo(); }

    /** @return array<int, array{label:string, diameterCm:string}> */
    public function findAllLabelDiameter(): array
    {
        $sql = "SELECT label, CAST(diameterCm AS CHAR) AS diameterCm FROM size ORDER BY id";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}