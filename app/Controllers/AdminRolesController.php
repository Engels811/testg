<?php

$oldPermissions = Database::fetchAll(
    'SELECT p.key_name
     FROM role_permissions rp
     JOIN permissions p ON p.id = rp.permission_id
     WHERE rp.role_id = ?',
    [$roleId]
);

Database::execute(
    'DELETE FROM role_permissions WHERE role_id = ?',
    [$roleId]
);

foreach ($permissions as $permId) {
    Database::execute(
        'INSERT INTO role_permissions (role_id, permission_id)
         VALUES (?, ?)',
        [$roleId, (int)$permId]
    );
}

$newPermissions = Database::fetchAll(
    'SELECT p.key_name
     FROM role_permissions rp
     JOIN permissions p ON p.id = rp.permission_id
     WHERE rp.role_id = ?',
    [$roleId]
);

AuditService::log(
    'role.permissions.updated',
    'role',
    $roleId,
    ['permissions' => array_column($oldPermissions, 'key_name')],
    ['permissions' => array_column($newPermissions, 'key_name')]
);
