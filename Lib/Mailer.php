<?php

namespace AcMarche\MarcheTail\Lib;

class Mailer
{
    public static function sendError(string $subject, string $message): void
    {
        $to = $_ENV['WEBMASTER_EMAIL'] ?? 'jf@marche.be';
        wp_mail($to, $subject, $message);
    }
}
