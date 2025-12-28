<?php
declare(strict_types=1);

require __DIR__ . '/../index.php';

Security::requireTeam();

header('Content-Type: application/json');

echo json_encode([
    'openReports' => (int) Database::fetchColumn(
        "SELECT COUNT(*) FROM reports WHERE status = 'open'"
    ),
    'openAppeals' => (int) Database::fetchColumn(
        "SELECT COUNT(*) FROM user_appeals WHERE status = 'open'"
    )
]);
