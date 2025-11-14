<?php

declare(strict_types=1);

namespace App\Service;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class Mailer
{
    private PHPMailer $mailer;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // ⚠️ fonction globale env() définie dans bootstrap.php
        $host   = \env('SMTP_HOST', 'localhost');
        $port   = (int) \env('SMTP_PORT', '25');
        $user   = \env('SMTP_USER', '');
        $pass   = \env('SMTP_PASS', '');
        $secure = \env('SMTP_SECURE', 'tls');

        $this->fromEmail = \env('MAIL_FROM', 'no-reply@example.com');
        $this->fromName  = \env('MAIL_FROM_NAME', 'Pizza App');

        // Debug SMTP en dev → log dans error_log
        if (defined('APP_ENV') && APP_ENV === 'dev') {
            $this->mailer->SMTPDebug  = 2;          // 0 en prod, 2 en dev
            $this->mailer->Debugoutput = 'error_log';
        }

        // Config Mailtrap
        $this->mailer->isSMTP();
        $this->mailer->Host       = $host;
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Port       = $port;
        $this->mailer->Username   = $user;
        $this->mailer->Password   = $pass;

        if ($secure === 'tls') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        } elseif ($secure === 'ssl') {
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } else {
            $this->mailer->SMTPSecure = '';
        }

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->isHTML(true);
        $this->mailer->setFrom($this->fromEmail, $this->fromName);
    }

    /**
     * Envoi d'un e-mail à partir d'un template PHP.
     *
     * @param string $to       Adresse du destinataire
     * @param string $subject  Sujet de l'e-mail
     * @param string $template Nom du fichier dans templates/email (sans .php)
     * @param array  $context  Variables accessibles dans le template
     */
    public function send(string $to, string $subject, string $template, array $context = []): bool
    {
        try {
            $html = $this->renderTemplate($template, $context);
            $text = $this->generateTextVersion($html);

            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $html;
            $this->mailer->AltBody = $text;

            $ok = $this->mailer->send();

            if (!$ok && defined('APP_ENV') && APP_ENV === 'dev') {
                error_log('Mailer::send() failed: ' . $this->mailer->ErrorInfo);
            }

            return $ok;
        } catch (Exception $e) {
            if (defined('APP_ENV') && APP_ENV === 'dev') {
                error_log('Mailer exception: ' . $e->getMessage() . ' / ' . $this->mailer->ErrorInfo);
            }
            return false;
        }
    }

    private function renderTemplate(string $template, array $context): string
    {
        $path = __DIR__ . '/../../templates/email/' . $template . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException("Template d'e-mail introuvable : {$template}");
        }

        extract($context, EXTR_SKIP);

        ob_start();
        include $path;
        return (string) ob_get_clean();
    }

    private function generateTextVersion(string $html): string
    {
        $text = strip_tags($html);
        return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    }
}