<?php
// Run once from the terminal:  php scripts/create_admin.php yourname "YourStrongPassword"
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('CLI only.'); }
require __DIR__ . '/../includes/functions.php';
[$_, $user, $pass] = $argv + [null, null, null];
if (!$user || !$pass || strlen($pass) < 10) exit("Usage: php scripts/create_admin.php <username> <password (10+ chars)>\n");
db()->prepare('INSERT INTO admins (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash)')
    ->execute([$user, password_hash($pass, PASSWORD_DEFAULT)]);
echo "Admin '$user' saved.\n";
