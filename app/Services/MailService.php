<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['SMTP_HOST'] ?? '';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['SMTP_USER'] ?? '';
        $this->mailer->Password   = $_ENV['SMTP_PASS'] ?? '';
        $this->mailer->SMTPSecure = $_ENV['SMTP_SECURE'] ?? 'tls'; // tls recomendado
        $this->mailer->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

        $this->mailer->CharSet = 'UTF-8';
    }

    /**
     * Envia convite para funcionário criar senha e ativar conta.
     */
    public function sendEmployeeInvite(string $to, string $name, string $inviteLink): bool
    {
        try {

            // Debug das env
            error_log("SMTP_ENV_CHECK: host=" . ($_ENV['SMTP_HOST'] ?? 'null') .
                " port=" . ($_ENV['SMTP_PORT'] ?? 'null') .
                " user=" . ($_ENV['SMTP_USER'] ?? 'null') .
                " secure=" . ($_ENV['SMTP_SECURE'] ?? 'null'));

            // debug SMTP real
            $this->mailer->SMTPDebug = 2;
            $this->mailer->Debugoutput = function ($line) {
                error_log("[SMTP_DEBUG] " . trim($line));
            };

            if (!$to || !$inviteLink) {
                error_log("MailService: Campos obrigatórios faltando.");
                return false;
            }

            $this->mailer->clearAllRecipients();

            $from = $_ENV['SMTP_FROM'] ?? $_ENV['SMTP_USER'] ?? null;
            $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Sistema';

            if (!$from) {
                throw new Exception("SMTP_FROM não configurado no .env");
            }

            $this->mailer->setFrom($from, $fromName);
            $this->mailer->addAddress($to, $name);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Convite para acessar o sistema';
            $this->mailer->Body = "..."; // omitido

            $result = $this->mailer->send();

            error_log("MAIL_SEND_RESULT=" . ($result ? "OK" : "FAIL"));

            return $result;
        } catch (Exception $e) {
            error_log("MailService ERROR: " . $e->getMessage());
            if (!empty($this->mailer->ErrorInfo)) {
                error_log("PHPMailer ErrorInfo: " . $this->mailer->ErrorInfo);
            }
            return false;
        }
    }
}
