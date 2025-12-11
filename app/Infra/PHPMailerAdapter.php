<?php

namespace App\Infra;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Core\MailerInterface;

class PHPMailerAdapter implements MailerInterface
{
    private PHPMailer $mailer;

    public function __construct()
    {
        // Config SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USER'];
        $this->mailer->Password   = $_ENV['MAIL_PASS'];
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = $_ENV['MAIL_PORT'] ?? 587;
    }


    public function send(string $to, string $subject, string $htmlBody, ?string $from = null, ?string $fromName = null): bool
    {
        try {
            $this->mailer->setFrom(
                $from ?? $_ENV['MAIL_FROM'],
                $fromName ?? $_ENV['MAIL_FROM_NAME']
            );

            $this->mailer->addAddress($to);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $htmlBody;

            return $this->mailer->send();
        } catch (Exception $e) {
            return false;
        }
    }
}
