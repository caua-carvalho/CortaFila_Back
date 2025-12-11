<?php

namespace App\Core;

interface MailerInterface
{
    public function send(
        string $to, 
        string $subject, 
        string $body,
        ?string $from = null,
        ?string $fromName = null
    ): bool;
}