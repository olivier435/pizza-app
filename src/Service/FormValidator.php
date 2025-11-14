<?php

declare(strict_types=1);

namespace App\Service;

use DateTimeImmutable;

final class FormValidator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    private function addError(string $field, string $message): void
    {
        // On ne garde que le premier message pour un champ
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = $message;
        }
    }

    public function required(string $field, string $message = 'Ce champ est obligatoire.'): self
    {
        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function minLength(string $field, int $min, string $message): self
    {
        if (isset($this->errors[$field])) {
            return $this; // on ne surcharge pas un champ déjà en erreur
        }

        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && mb_strlen($value) < $min) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function email(string $field, string $message = 'Email invalide.'): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string)($this->data[$field] ?? ''));
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function phoneMinDigits(string $field, int $minDigits, string $message): self
    {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string)($this->data[$field] ?? ''));
        $digits = preg_replace('/\D+/', '', $value);
        if ($digits === null || strlen($digits) < $minDigits) {
            $this->addError($field, $message);
        }
        return $this;
    }

    public function dateNotPast(
        string $field,
        string $messageInvalid = 'Date invalide.',
        string $messagePast    = 'La date doit être égale ou postérieure à aujourd\'hui.'
    ): self {
        if (isset($this->errors[$field])) {
            return $this;
        }

        $value = trim((string)($this->data[$field] ?? ''));
        if ($value === '') {
            return $this;
        }

        try {
            $bookingDate = new DateTimeImmutable($value);
            $today = (new DateTimeImmutable('today'))->setTime(0, 0);
            if ($bookingDate < $today) {
                $this->addError($field, $messagePast);
            }
        } catch (\Throwable) {
            $this->addError($field, $messageInvalid);
        }

        return $this;
    }
}