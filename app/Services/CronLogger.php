<?php
declare(strict_types=1);

class CronLogger
{
    public static function log(
        string $job,
        string $status,
        ?string $message = null,
        ?int $runtimeMs = null
    ): void {
        Database::execute(
            "INSERT INTO cron_logs (job, status, message, runtime_ms)
             VALUES (?, ?, ?, ?)",
            [$job, $status, $message, $runtimeMs]
        );
    }

    public static function lastRun(string $job): ?array
    {
        return Database::fetch(
            "SELECT * FROM cron_logs
             WHERE job = ?
             ORDER BY created_at DESC
             LIMIT 1",
            [$job]
        ) ?: null;
    }
}
