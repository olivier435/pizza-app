<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\Controller;

final class HomeController extends Controller
{
    public function index(): void
    {
        $pageTitle = 'Accueil — Back Pizza';
        $this->render('home/index', compact('pageTitle'));
    }
}