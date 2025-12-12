<?php

namespace App\Services;

class MailService
{
    private string $apiKey;
    private string $from;

    public function __construct()
    {
        $this->apiKey = $_ENV['RESEND_API_KEY'] ?? '';
        $this->from   = $_ENV['MAIL_FROM'] ?? 'no-reply@cortafila.com';

        if (!$this->apiKey) {
            error_log("MailService ERROR: RESEND_API_KEY ausente.");
        }
    }

    /**
     * Envia convite para funcionário criar senha e ativar conta.
     */
    public function sendEmployeeInvite(string $to, string $name, string $inviteLink): bool
    {
        if (!$to || !$inviteLink) {
            error_log("MailService ERROR: parâmetros obrigatórios faltando.");
            return false;
        }

        $html = $this->buildInviteTemplate($name, $inviteLink);

        $payload = [
            "from"    => $this->from,
            "to"      => [$to],
            "subject" => "Convite para acessar o sistema CortaFila",
            "html"    => $html
        ];

        $ch = curl_init("https://api.resend.com/emails");

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $error    = curl_error($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            error_log("Resend CURL_ERROR: " . $error);
            return false;
        }

        if ($status >= 400) {
            error_log("Resend API ERROR: HTTP {$status} - {$response}");
            return false;
        }

        return true;
    }

    /**
     * Template do e-mail (HTML).
     */
    private function buildInviteTemplate(string $name, string $inviteLink): string
    {
        return "
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
    }
}
