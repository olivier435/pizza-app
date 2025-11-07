<?php

namespace App\Repository;

use App\Core\DB;
use PDO;

abstract class Repository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::pdo();
    }
}