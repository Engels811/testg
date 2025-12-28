<?php
declare(strict_types=1);

class ReportController
{
    public function create(): void
    {
        if (empty($_SESSION['user']['id'])) {
            header('Location: /login');
            exit;
        }

        if (!isset($_POST['csrf']) || !Csrf::validate($_POST['csrf'])) {
            http_response_code(403);
            exit('Invalid CSRF token');
        }

        $contentType = $_POST['content_type'] ?? null;
        $contentId   = $_POST['content_id'] ?? null;
        $reason      = $_POST['reason'] ?? null;
        $message     = $_POST['message'] ?? null;

        if (!$contentType || !$contentId || !$reason) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        $allowedTypes   = ['gallery', 'forum', 'video'];
        $allowedReasons = ['spam','beleidigung','hate','nsfw','copyright','fake','other'];

        if (
            !in_array($contentType, $allowedTypes, true) ||
            !in_array($reason, $allowedReasons, true)
        ) {
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }

        // 🔑 WICHTIG: gleiche DB-Instanz wie Admin
        $db = Database::getInstance();

        $stmt = $db->prepare(
            "INSERT INTO reports
             (content_type, content_id, reason, message, reported_by, status, created_at)
             VALUES (?, ?, ?, ?, ?, 'open', NOW())"
        );

        $stmt->execute([
            $contentType,
            (int)$contentId,
            $reason,
            $message,
            (int)$_SESSION['user']['id']
        ]);

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}
