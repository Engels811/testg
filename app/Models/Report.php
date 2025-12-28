<?php
declare(strict_types=1);

class Report
{
    /* =========================
       REPORT LISTE
    ========================= */

    /**
     * Alle Reports (Admin / Moderator / Superadmin)
     */
    public static function all(): array
    {
        $reports = Database::fetchAll(
            "SELECT
                r.*,
                u.username AS reporter_name
             FROM reports r
             LEFT JOIN users u ON u.id = r.reported_by
             ORDER BY r.created_at DESC"
        );

        return $reports ?: [];
    }

    /**
     * Einzelnen Report laden
     */
    public static function find(int $id): ?array
    {
        $report = Database::fetch(
            "SELECT
                r.*,
                u.username AS reporter_name
             FROM reports r
             LEFT JOIN users u ON u.id = r.reported_by
             WHERE r.id = ?",
            [$id]
        );

        return $report ?: null;
    }

    /* =========================
       ASSIGN / STATUS
    ========================= */

    /**
     * Report einem Teammitglied zuweisen
     */
    public static function assign(int $reportId, int $userId): void
    {
        Database::execute(
            "UPDATE reports
             SET assigned_to = ?, status = 'in_review'
             WHERE id = ?",
            [$userId, $reportId]
        );
    }

    /**
     * Status + Aktion setzen
     */
    public static function updateStatus(
        int $reportId,
        string $status,
        string $action
    ): void {
        Database::execute(
            "UPDATE reports
             SET status = ?, action = ?
             WHERE id = ?",
            [$status, $action, $reportId]
        );
    }

    /* =========================
       REPORT ERSTELLEN
    ========================= */

    /**
     * Neuen Report anlegen
     * + Auto-Review prüfen (3 Reports)
     */
    public static function create(
        string $targetUsername,
        int $reportedBy,
        string $reason,
        string $reportType = 'user'
    ): void {
        Database::execute(
            "INSERT INTO reports
             (target_username, reported_by, reason, report_type, status)
             VALUES (?, ?, ?, ?, 'open')",
            [$targetUsername, $reportedBy, $reason, $reportType]
        );

        // 🔥 Auto-Review prüfen
        self::autoReviewIfNeeded($targetUsername);
    }

    /* =========================
       REPORT → STRAFE
    ========================= */

    /**
     * Report bearbeiten & User bestrafen
     * (Warn / Mute / Suspend / Ban)
     */
    public static function punish(
        int $reportId,
        string $targetUsername,
        string $action,
        string $actorUsername,
        ?int $durationMinutes = null,
        ?string $reason = null
    ): void {
        // 1. Moderations-Aktion speichern
        Database::execute(
            "INSERT INTO user_actions
             (username, action, reason, duration_minutes, expires_at, created_by)
             VALUES (?, ?, ?, ?, ?, ?)",
            [
                $targetUsername,
                $action,
                $reason,
                $durationMinutes,
                $durationMinutes
                    ? date('Y-m-d H:i:s', time() + ($durationMinutes * 60))
                    : null,
                $actorUsername
            ]
        );

        // 2. User benachrichtigen
        UserNotification::create(
            $targetUsername,
            'Moderationsmaßnahme',
            "Aktion: {$action}\nGrund: {$reason}",
            'warning'
        );

        // 3. Report schließen
        Database::execute(
            "UPDATE reports
             SET status = 'closed', action = ?
             WHERE id = ?",
            [$action, $reportId]
        );

        // 4. Log schreiben
        self::log(
            $reportId,
            Auth::id(),
            Auth::user()->role,
            'punish',
            "Action: {$action}"
        );
    }

    /* =========================
       AUTO-REVIEW (3 REPORTS)
    ========================= */

    /**
     * Ab 3 offenen Reports → Auto-Review
     */
    public static function autoReviewIfNeeded(string $username): void
    {
        $count = Database::fetchColumn(
            "SELECT COUNT(*)
             FROM reports
             WHERE target_username = ?
             AND status = 'open'",
            [$username]
        );

        if ($count >= 3) {
            Database::execute(
                "INSERT INTO user_actions
                 (username, action, reason, created_by)
                 VALUES (?, 'review', 'Auto-Review (3 Reports)', 'System')",
                [$username]
            );

            UserNotification::create(
                $username,
                'Account wird geprüft',
                'Dein Account wird aufgrund mehrerer Reports überprüft.',
                'info'
            );
        }
    }

    /* =========================
       LOGGING
    ========================= */

    /**
     * Report-Log (Audit-Trail)
     */
    public static function log(
        int $reportId,
        int $actorId,
        string $actorRole,
        string $action,
        ?string $note = null
    ): void {
        Database::execute(
            "INSERT INTO report_logs
             (report_id, action, actor_id, actor_role, note)
             VALUES (?, ?, ?, ?, ?)",
            [$reportId, $action, $actorId, $actorRole, $note]
        );
    }
}
