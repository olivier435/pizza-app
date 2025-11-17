<?php

use App\Entity\Pizza;

/**
 * @param Pizza $pizza
 * @param array $errors
 */
function renderPizzaPhotoField(Pizza $pizza, array $errors = []): void
{
    // On expose $currentPhoto au partial _photo.php
    $currentPhoto = $pizza->getPhoto();
    include __DIR__ . '/_photo.php';
}