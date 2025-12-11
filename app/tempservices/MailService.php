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
            // validação básica
            if (!$to || !$inviteLink) {
                return false;
            }

            // clear em caso de reuso
            $this->mailer->clearAllRecipients();

            // remetente
            $from = $_ENV['SMTP_FROM'] ?? $_ENV['SMTP_USER'] ?? null;
            $fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Sistema';

            if (!$from) {
                throw new Exception("SMTP_FROM não configurado no .env");
            }

            $this->mailer->setFrom($from, $fromName);

            // destinatário
            $this->mailer->addAddress($to, $name);

            // corpo
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Convite para acessar o sistema';

            $this->mailer->Body = "
            <table width='100%' cellpadding='0' cellspacing='0' style='font-family: Montserrat, sans-serif; background:#1f1f1f; color:#fff; padding:20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background:#2b2b2b; border-radius:10px; overflow:hidden;'>
                            <tr>
                                <td style='background:#10b981; padding:20px; text-align:center; color:#fff; font-size:24px; font-weight:bold;'>
                                    Convite de acesso para CortaFila
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:30px; font-size:16px; line-height:1.5;'>
                                    <p>Olá <strong>{$name}</strong>,</p>
                                    <p>Você foi adicionado como funcionário na plataforma <strong>CortaFila</strong>.</p>
                                    <p>Para configurar sua senha e ativar seu acesso, clique no botão abaixo:</p>
                                    <p style='text-align:center; margin:30px 0;'>
                                        <a href='{$inviteLink}' 
                                        style='display:inline-block; padding:12px 20px; background:#10b981; color:#fff; font-weight:bold; text-decoration:none; border-radius:8px;'>
                                        Criar senha
                                        </a>
                                    </p>
                                    <p style='text-align:center; font-size:14px; color:#aaa;'>Ou copie e cole no navegador:<br/>
                                    <a href='{$inviteLink}' style='color:#4f46e5; word-break:break-all;'>{$inviteLink}</a></p>
                                    <p style='margin-top:20px; font-size:14px; color:#aaa;'>Este link expira em 48 horas.</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='background:#111; text-align:center; padding:15px; font-size:12px; color:#888;'>
                                    &copy; " . date('Y') . " CortaFila. Todos os direitos reservados.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            ";


            return $this->mailer->send();
        } catch (Exception $e) {
            error_log('MailService ERROR: ' . $e->getMessage());
            return false;
        }
    }
}
