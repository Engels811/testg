<?php
Security::requireAdmin();
Database::execute(
 "UPDATE games SET category=? WHERE id=?",
 [$_POST['cat'], $_POST['id']]
);
