<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Mailer;
use App\Core\Controller;
use App\Service\FormValidator;

final class BookingController extends Controller
{
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $pageTitle = 'Réserver une table';

        // Données/erreurs éventuellement stockées par send()
        $old    = $_SESSION['booking_old']    ?? [];
        $errors = $_SESSION['booking_errors'] ?? [];
        unset($_SESSION['booking_old'], $_SESSION['booking_errors']);

        $this->render('booking/index', [
            'pageTitle' => $pageTitle,
            'old'       => $old,
            'errors'    => $errors,
        ]);
    }

    public function send(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Récupération des données brutes
        $data = [
            'name'     => trim((string)($_POST['name']     ?? '')),
            'email'    => trim((string)($_POST['email']    ?? '')),
            'phone'    => trim((string)($_POST['phone']    ?? '')),
            'date'     => trim((string)($_POST['date']     ?? '')),
            'time'     => trim((string)($_POST['time']     ?? '')),
            'people'   => trim((string)($_POST['people']   ?? '')),
            'occasion' => trim((string)($_POST['occasion'] ?? '')),
            'message'  => trim((string)($_POST['message']  ?? '')),
        ];

        $validator = new FormValidator($data);

        $validator
            ->required('name', 'Merci de renseigner votre nom complet.')
            ->minLength('name', 3, 'Merci de renseigner votre nom complet (au moins 3 caractères).')

            ->required('email', 'Merci de renseigner une adresse email.')
            ->email('email', 'Merci de renseigner une adresse email valide.')

            ->required('phone', 'Merci de renseigner un numéro de téléphone.')
            ->phoneMinDigits('phone', 6, 'Merci de renseigner un numéro de téléphone valide.')

            ->required('date', 'Merci de choisir une date.')
            ->dateNotPast(
                'date',
                'Date invalide.',
                'La date de réservation doit être égale ou postérieure à aujourd\'hui.'
            )

            ->required('time', 'Merci de choisir une heure.')

            ->required('people', 'Merci d\'indiquer le nombre de convives.')
            // people: on se contente de "non vide" côté back, les options sont contrôlées dans le HTML

            // message optionnel, mais si présent : min 4 caractères
            ->minLength('message', 4, 'Votre message est trop court (min. 4 caractères) ou laissez-le vide.')
        ;

        $errors = $validator->getErrors();

        if ($validator->hasErrors()) {
            $_SESSION['booking_old']    = $data;
            $_SESSION['booking_errors'] = $errors;
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Merci de corriger les erreurs du formulaire de réservation.',
            ];
            $this->redirect('/booking');
            return;
        }

        try {
            $mailer = new Mailer();

            // Destinataire configurable (sinon fallback sur MAIL_FROM)
            $to = env('MAIL_BOOKING_TO', env('MAIL_FROM', 'no-reply@example.com'));

            $mailer->send(
                $to,
                'Nouvelle demande de réservation',
                'booking_request',
                ['data' => $data]
            );

            $_SESSION['_flash'][] = [
                'type' => 'success',
                'msg'  => 'Votre demande de réservation a bien été envoyée. Nous vous confirmerons au plus vite.',
            ];
        } catch (\Throwable) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Une erreur est survenue lors de l'envoi de votre demande. Veuillez réessayer plus tard.",
            ];
        }

        $this->redirect('/booking');
    }
}