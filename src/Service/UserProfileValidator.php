<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepository;

final class UserProfileValidator
{
    public function __construct(private UserRepository $repo) {}

    /**
     * Valide et retourne un tableau d'erreurs (vide si OK).
     */
    public function validateProfileUpdate(
        string $firstname,
        string $lastname,
        string $email,
        string $phone,
        int $excludeUserId
    ): array {
        $errors = [];

        // Trim
        $firstname = trim($firstname);
        $lastname  = trim($lastname);
        $email     = trim($email);
        $phone     = trim($phone);

        if (mb_strlen($firstname) < 2) {
            $errors[] = "Le prénom doit contenir au moins 2 caractères.";
        }
        if (mb_strlen($lastname) < 2) {
            $errors[] = "Le nom doit contenir au moins 2 caractères.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse e-mail invalide.";
        }

        if ($phone !== '') {
            $digits = preg_replace('~\D+~', '', $phone);
            if (strlen($digits) < 8) {
                $errors[] = "Numéro de téléphone invalide.";
            }
        }

        if ($email !== '' && $this->repo->isEmailTakenByOther($email, $excludeUserId)) {
            $errors[] = "Cette adresse e-mail est déjà utilisée.";
        }

        return $errors;
    }
}