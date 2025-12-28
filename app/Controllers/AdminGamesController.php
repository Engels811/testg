<?php
declare(strict_types=1);

class AdminGamesController
{
    /* =========================================================
       ACCESS GUARD (PERMISSION-BASIERT)
    ========================================================= */

    private function guard(string $permission): void
    {
        if (
            empty($_SESSION['user']) ||
            !Permission::has($permission)
        ) {
            http_response_code(403);
            View::render('errors/403', [
                'title' => 'Zugriff verweigert'
            ]);
            exit;
        }
    }

    /**
     * GET /admin/games
     * Übersicht & Verwaltung aller Spiele
     */
    public function index(): void
    {
        $this->guard('admin.games.manage');

        $games = Database::fetchAll(
            "SELECT
                id,
                name,
                slug,
                provider,
                provider_id,
                category,
                hours,
                hours_override,
                cover,
                is_top,
                confirmed,
                last_played,
                created_at,
                updated_at
             FROM games
             ORDER BY
                is_top DESC,
                confirmed DESC,
                updated_at DESC"
        ) ?? [];

        View::render(
            'admin/games/index',
            [
                'title' => 'Spiele verwalten',
                'games' => $games
            ],
            'admin'
        );
    }

    /**
     * GET /admin/games/edit/{id}
     */
    public function edit(int $id): void
    {
        $this->guard('admin.games.manage');

        $game = Database::fetch(
            "SELECT * FROM games WHERE id = ?",
            [$id]
        );

        if (!$game) {
            http_response_code(404);
            View::render('errors/404', [
                'title' => 'Spiel nicht gefunden'
            ]);
            return;
        }

        $categoriesFile = BASE_PATH . '/config/game_categories.php';
        $categories = file_exists($categoriesFile)
            ? require $categoriesFile
            : [];

        View::render(
            'admin/games/edit',
            [
                'title'      => 'Spiel bearbeiten',
                'game'       => $game,
                'categories' => $categories
            ],
            'admin'
        );
    }

    /**
     * POST /admin/games/update
     */
    public function update(): void
    {
        $this->guard('admin.games.manage');
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit('Invalid game ID');
        }

        $category = $_POST['category'] ?? null;
        $cover    = $_POST['cover'] ?? null;
        $provider = $_POST['provider'] ?? 'steam';

        $hoursOverride = ($_POST['hours_override'] ?? '') !== ''
            ? (float)$_POST['hours_override']
            : null;

        $isTop     = isset($_POST['is_top']) ? 1 : 0;
        $confirmed = isset($_POST['confirmed']) ? 1 : 0;

        Database::execute(
            "UPDATE games SET
                category       = ?,
                cover          = ?,
                hours_override = ?,
                is_top         = ?,
                confirmed      = ?,
                provider       = ?,
                updated_at     = NOW()
             WHERE id = ?",
            [
                $category !== '' ? $category : null,
                $cover !== '' ? $cover : null,
                $hoursOverride,
                $isTop,
                $confirmed,
                $provider,
                $id
            ]
        );

        $_SESSION['flash_success'] = 'Spiel wurde aktualisiert.';
        header('Location: /admin/games');
        exit;
    }

    /**
     * POST /admin/games/toggle
     * AJAX Toggle: confirmed / is_top
     */
    public function toggle(): void
    {
        $this->guard('admin.games.manage');
        Security::checkCsrf();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id    = (int)($_POST['id'] ?? 0);
        $field = $_POST['field'] ?? '';

        if ($id <= 0 || !in_array($field, ['confirmed', 'is_top'], true)) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            "UPDATE games
             SET {$field} = 1 - {$field},
                 updated_at = NOW()
             WHERE id = ?",
            [$id]
        );

        // Frontend erwartet 204 (No Content)
        http_response_code(204);
        exit;
    }
}
