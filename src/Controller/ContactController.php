<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Mailer;
use App\Core\Controller;
use App\Service\FormValidator;

final class ContactController extends Controller
{
    public function index(): void
    {
        $pageTitle = 'Nous contacter';

        // Données/erreurs éventuellement stockées par send()
        $old    = $_SESSION['contact_old']    ?? [];
        $errors = $_SESSION['contact_errors'] ?? [];
        unset($_SESSION['contact_old'], $_SESSION['contact_errors']);

        $this->render('contact/index', [
            'pageTitle'   => $pageTitle,
            'old'       => $old,
            'errors'    => $errors,
        ]);
    }

    public function send(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $data = [
            'firstname' => trim((string)($_POST['firstname'] ?? '')),
            'lastname'  => trim((string)($_POST['lastname']  ?? '')),
            'email'     => trim((string)($_POST['email']     ?? '')),
            'subject'   => trim((string)($_POST['subject']   ?? '')),
            'message'   => trim((string)($_POST['message']   ?? '')),
        ];

        $validator = new FormValidator($data);

        $validator
            ->required('firstname', 'Merci de renseigner un prénom.')
            ->minLength('firstname', 2, 'Merci de renseigner un prénom (au moins 2 caractères).')

            ->required('lastname', 'Merci de renseigner un nom.')
            ->minLength('lastname', 2, 'Merci de renseigner un nom (au moins 2 caractères).')

            ->required('email', 'Merci de renseigner une adresse email.')
            ->email('email', 'Merci de renseigner une adresse email valide.')

            ->required('subject', 'Merci de renseigner un sujet.')
            ->minLength('subject', 3, 'Le sujet doit contenir au moins 3 caractères.')

            ->required('message', 'Merci de renseigner un message.')
            ->minLength('message', 4, 'Le message doit contenir au moins 4 caractères.')
        ;

        $errors = $validator->getErrors();

        if ($validator->hasErrors()) {
            $_SESSION['contact_old']    = $data;
            $_SESSION['contact_errors'] = $errors;
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => 'Merci de corriger les erreurs du formulaire.',
            ];
            $this->redirect('/contact');
            return;
        }

        // Envoi de l'email
        try {
            $mailer = new Mailer();

            // Destinataire (admin), configurable via .env
            $to = env('MAIL_CONTACT_TO', env('MAIL_FROM', 'no-reply@example.com'));

            $mailer->send(
                $to,
                'Nouveau message de contact - ' . $data['subject'],
                'contact',
                ['data' => $data]
            );

            $_SESSION['_flash'][] = [
                'type' => 'success',
                'msg'  => 'Merci, votre message a bien été envoyé.',
            ];
        } catch (\Throwable $e) {
            $_SESSION['_flash'][] = [
                'type' => 'danger',
                'msg'  => "Une erreur est survenue lors de l'envoi de votre message. Veuillez réessayer plus tard.",
            ];
        }

        $this->redirect('/contact');
    }
}