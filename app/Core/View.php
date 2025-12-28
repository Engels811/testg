<?php
declare(strict_types=1);

final class View
{
    /**
     * Normales View-Rendering MIT Layout
     */
    public static function render(string $view, array $data = []): void
    {
        if (!defined('BASE_PATH')) {
            throw new RuntimeException('BASE_PATH ist nicht definiert');
        }

        // Variablen für das View bereitstellen
        extract($data, EXTR_SKIP);

        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException("View nicht gefunden: {$view}");
        }

        // Admin-Views erkennen (z. B. admin/...)
        $isAdmin = str_starts_with($view, 'admin/');

        // Layout auswählen
        $layout = $isAdmin
            ? BASE_PATH . '/app/Views/layouts/admin.php'
            : BASE_PATH . '/app/Views/layouts/base.php';

        if (!is_file($layout)) {
            throw new RuntimeException('Layout nicht gefunden');
        }

        // View puffern
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Layout laden (nutzt $content)
        require $layout;
    }

    /**
     * Partial-Rendering OHNE Layout
     * (z. B. E-Mails, Snippets, AJAX-Fragmente)
     */
    public static function renderPartial(string $view, array $data = []): string
    {
        if (!defined('BASE_PATH')) {
            throw new RuntimeException('BASE_PATH ist nicht definiert');
        }

        extract($data, EXTR_SKIP);

        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';

        if (!is_file($viewFile)) {
            throw new RuntimeException("View-Partial nicht gefunden: {$view}");
        }

        ob_start();
        require $viewFile;
        return ob_get_clean();
    }
}
