<?php
declare(strict_types=1);

final class MailLog
{
    public static function log(array $data): void
    {
        Database::execute(
            "INSERT INTO mail_logs
             (direction, recipient_email, recipient_name, subject, message, sent_by_user_id, status)
             VALUES (:direction, :email, :name, :subject, :message, :user_id, :status)",
            [
                'direction' => 'outgoing',
                'email'     => $data['email'],
                'name'      => $data['name'] ?? null,
                'subject'   => $data['subject'],
                'message'   => $data['message'],
                'user_id'   => $data['user_id'] ?? null,
                'status'    => $data['status'],
            ]
        );
    }
}
