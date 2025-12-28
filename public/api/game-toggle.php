<?php
Security::requireAdmin();
Database::execute(
 "UPDATE games SET {$_POST['field']}=1-{$_POST['field']} WHERE id=?",
 [$_POST['id']]
);
