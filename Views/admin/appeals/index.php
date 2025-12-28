<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Core\View;
use App\Core\Database;
use App\Core\Csrf;
use App\Core\Session;
use PDO;

class AdminAppealController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();

        // Admin-Check
        if (!Session::isAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * GET /admin/appeals
     * Zeigt alle Einsprüche an
     */
    public function index(): void
    {
        $stmt = $this->db->query("
            SELECT 
                a.*,
                u.username,
                admin.username AS resolved_by
            FROM appeals a
            LEFT JOIN users u ON a.user_id = u.id
            LEFT JOIN users admin ON a.resolved_by_id = admin.id
            ORDER BY 
                CASE a.status
                    WHEN 'open' THEN 1
                    WHEN 'approved' THEN 2
                    WHEN 'rejected' THEN 3
                END,
                a.created_at DESC
        ");

        $appeals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        View::render('admin/appeals/index', [
            'title'   => 'Einsprüche',
            'appeals' => $appeals
        ]);
    }

    /**
     * POST /admin/appeals/{id}/approve
     */
    public function approve(int $id): void
    {
        Csrf::validateOrFail();

        $adminId = (int) Session::get('user_id');

        $stmt = $this->db->prepare("
            UPDATE appeals 
            SET 
                status = 'approved',
                resolved_by_id = ?,
                resolved_at = NOW()
            WHERE id = ? AND status = 'open'
        ");

        $stmt->execute([$adminId, $id]);

        if ($stmt->rowCount() === 1) {
            Session::setFlash('success', 'Einspruch wurde angenommen');
        } else {
            Session::setFlash('error', 'Einspruch konnte nicht aktualisiert werden');
        }

        header('Location: /admin/appeals');
        exit;
    }

    /**
     * POST /admin/appeals/{id}/reject
     */
    public function reject(int $id): void
    {
        Csrf::validateOrFail();

        $adminId = (int) Session::get('user_id');

        $stmt = $this->db->prepare("
            UPDATE appeals 
            SET 
                status = 'rejected',
                resolved_by_id = ?,
                resolved_at = NOW()
            WHERE id = ? AND status = 'open'
        ");

        $stmt->execute([$adminId, $id]);

        if ($stmt->rowCount() === 1) {
            Session::setFlash('success', 'Einspruch wurde abgelehnt');
        } else {
            Session::setFlash('error', 'Einspruch konnte nicht aktualisiert werden');
        }

        header('Location: /admin/appeals');
        exit;
    }
}
