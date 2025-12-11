<?php

namespace App\Services;

use App\Core\MailerInterface;

class EmailService
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcome(string $email, string $name): bool
    {
        $subject = "Bem-vindo, $name!";
        $body = "<h1>Olá, $name</h1><p>Seja bem-vindo ao sistema!</p>";

        return $this->mailer->send($email, $subject, $body);
    }
}
