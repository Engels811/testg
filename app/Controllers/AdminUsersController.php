$oldRole = Database::fetch(
    'SELECT r.id, r.name, r.level
     FROM roles r
     JOIN users u ON u.role_id = r.id
     WHERE u.id = ?',
    [$userId]
);

Database::execute(
    'UPDATE users SET role_id = ? WHERE id = ?',
    [$roleId, $userId]
);

$newRole = Database::fetch(
    'SELECT id, name, level FROM roles WHERE id = ?',
    [$roleId]
);

AuditService::log(
    'user.role.changed',
    'user',
    $userId,
    $oldRole,
    $newRole
);
