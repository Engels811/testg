<?php
declare(strict_types=1);

final class MailTemplate
{
    public static function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require BASE_PATH . '/app/Mail/Templates/' . $template . '.php';
        return ob_get_clean();
    }
}
