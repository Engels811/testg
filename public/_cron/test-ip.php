<?php
header('Content-Type: text/plain');
echo "Deine IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unbekannt') . "\n";
echo "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unbekannt') . "\n";