<?php
declare(strict_types=1);

final class Mailer
{
    public static function send(
        string $to,
        string $subject,
        string $html,
        string $from = 'Engels811 TV <no-reply@engels811-ttv.de>'
    ): bool {
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $from;
        $headers[] = 'Reply-To: no-reply@engels811-ttv.de';

        return mail(
            $to,
            $subject,
            $html,
            implode("\r\n", $headers)
        );
    }
}
