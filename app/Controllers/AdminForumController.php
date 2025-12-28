<?php
declare(strict_types=1);

class AdminForumController
{
    public function index(): void
    {
        Security::requireModerator();

        $stats = [
            'threads'     => Database::fetch('SELECT COUNT(*) AS count FROM forum_threads')['count'] ?? 0,
            'posts'       => Database::fetch('SELECT COUNT(*) AS count FROM forum_posts WHERE is_deleted = 0')['count'] ?? 0,
            'categories'  => Database::fetch('SELECT COUNT(*) AS count FROM forum_categories')['count'] ?? 0,
        ];

        $recentThreads = Database::fetchAll(
            'SELECT t.*, u.username, c.title AS category_title
             FROM forum_threads t
             JOIN users u ON u.id = t.user_id
             JOIN forum_categories c ON c.id = t.category_id
             ORDER BY t.created_at DESC
             LIMIT 20'
        ) ?? [];

        View::render('admin/forum/index', [
            'title'   => 'Forum Verwaltung',
            'stats'   => $stats,
            'threads' => $recentThreads
        ]);
    }

    public function toggleSticky(): void
    {
        Security::requireModerator();
        Security::checkCsrf();

        $threadId = (int)($_POST['id'] ?? 0);
        if ($threadId <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            'UPDATE forum_threads
             SET is_sticky = 1 - is_sticky
             WHERE id = ?',
            [$threadId]
        );

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/forum'));
        exit;
    }

    public function toggleLock(): void
    {
        Security::requireModerator();
        Security::checkCsrf();

        $threadId = (int)($_POST['id'] ?? 0);
        if ($threadId <= 0) {
            http_response_code(400);
            exit;
        }

        Database::execute(
            'UPDATE forum_threads
             SET is_locked = 1 - is_locked
             WHERE id = ?',
            [$threadId]
        );

        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/admin/forum'));
        exit;
    }

    public function deleteThread(): void
    {
        Security::requireModerator();
        Security::checkCsrf();

        $threadId = (int)($_POST['id'] ?? 0);
        if ($threadId <= 0) {
            http_response_code(400);
            exit;
        }

        $posts = Database::fetchAll(
            'SELECT id FROM forum_posts WHERE thread_id = ?',
            [$threadId]
        ) ?? [];

        foreach ($posts as $post) {
            ForumAttachmentService::deleteByPost((int)$post['id']);
        }

        Database::execute(
            'DELETE FROM forum_posts WHERE thread_id = ?',
            [$threadId]
        );

        Database::execute(
            'DELETE FROM forum_threads WHERE id = ?',
            [$threadId]
        );

        $_SESSION['flash_success'] = 'Thread wurde gelöscht.';
        header('Location: /admin/forum');
        exit;
    }

    public function categories(): void
    {
        Security::requireAdmin();

        $categories = Database::fetchAll(
            'SELECT c.*,
                    (SELECT COUNT(*) FROM forum_threads WHERE category_id = c.id) AS thread_count
             FROM forum_categories c
             ORDER BY c.title'
        ) ?? [];

        View::render('admin/forum/categories', [
            'title'      => 'Kategorien verwalten',
            'categories' => $categories
        ]);
    }

    public function createCategory(): void
    {
        Security::requireAdmin();

        View::render('admin/forum/category-form', [
            'title'    => 'Kategorie erstellen',
            'category' => null
        ]);
    }

    public function storeCategory(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $title = trim($_POST['title'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $desc  = trim($_POST['description'] ?? '');

        if ($title === '' || $slug === '') {
            $_SESSION['flash_error'] = 'Titel und Slug sind erforderlich.';
            header('Location: /admin/forum/categories/create');
            exit;
        }

        $exists = Database::fetch(
            'SELECT id FROM forum_categories WHERE slug = ?',
            [$slug]
        );

        if ($exists) {
            $_SESSION['flash_error'] = 'Slug existiert bereits.';
            header('Location: /admin/forum/categories/create');
            exit;
        }

        Database::execute(
            'INSERT INTO forum_categories (title, slug, description)
             VALUES (?, ?, ?)',
            [$title, $slug, $desc]
        );

        header('Location: /admin/forum/categories');
        exit;
    }

    public function editCategory(int $id): void
    {
        Security::requireAdmin();

        $category = Database::fetch(
            'SELECT * FROM forum_categories WHERE id = ?',
            [$id]
        );

        if (!$category) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('admin/forum/category-form', [
            'title'    => 'Kategorie bearbeiten',
            'category' => $category
        ]);
    }

    public function updateCategory(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id    = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug  = trim($_POST['slug'] ?? '');
        $desc  = trim($_POST['description'] ?? '');

        if ($id <= 0 || $title === '' || $slug === '') {
            http_response_code(400);
            exit;
        }

        $exists = Database::fetch(
            'SELECT id FROM forum_categories WHERE slug = ? AND id != ?',
            [$slug, $id]
        );

        if ($exists) {
            $_SESSION['flash_error'] = 'Slug existiert bereits.';
            header('Location: /admin/forum/categories/edit/' . $id);
            exit;
        }

        Database::execute(
            'UPDATE forum_categories
             SET title = ?, slug = ?, description = ?
             WHERE id = ?',
            [$title, $slug, $desc, $id]
        );

        header('Location: /admin/forum/categories');
        exit;
    }

    public function deleteCategory(): void
    {
        Security::requireAdmin();
        Security::checkCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            exit;
        }

        $threadCount = Database::fetch(
            'SELECT COUNT(*) A*
