<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/app/config/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

load_env(__DIR__ . '/.env');

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USER'];
    $mail->Password   = $_ENV['MAIL_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int) $_ENV['MAIL_PORT'];

    $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_FROM_NAME']);
    $mail->addAddress("caua.carma033@gmail.com" ?? $_ENV['MAIL_USER']);

    $mail->isHTML(true);
    $mail->Subject = "Teste SMTP";
    $mail->Body    = "<h1>Email enviado com sucesso!</h1>";

    $mail->send();

    echo "OK: email enviado.";
} catch (Exception $e) {
    echo "ERRO: " . $mail->ErrorInfo;
}
