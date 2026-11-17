<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_admin()) redirect('admin/index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $ip = client_hash();
    $st = db()->prepare('SELECT COUNT(*) FROM login_attempts WHERE ip_hash = ? AND attempted_at > (NOW() - INTERVAL 15 MINUTE)');
    $st->execute([$ip]);
    if ((int)$st->fetchColumn() >= cfg('max_login_attempts')) {
        $error = 'Too many attempts. Try again in 15 minutes.';
    } else {
        $st = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
        $st->execute([trim((string)($_POST['username'] ?? ''))]);
        $row = $st->fetch();
        // Always run password_verify so response time doesn't reveal whether the user exists
        $hash = $row['password_hash'] ?? '$2y$10$usesomesillystringforsaltandhashingxxxxxxxxxxxxxxxxxxxxxx';
        if (password_verify((string)($_POST['password'] ?? ''), $hash) && $row) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int)$row['id'];
            db()->prepare('DELETE FROM login_attempts WHERE ip_hash = ?')->execute([$ip]);
            redirect('admin/index.php');
        }
        db()->prepare('INSERT INTO login_attempts (ip_hash) VALUES (?)')->execute([$ip]);
        $error = 'Invalid username or password.';
    }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin login</title><link rel="stylesheet" href="<?= e(url('css/style.css')) ?>"><script src="<?= e(url('js/theme.js')) ?>"></script></head>
<body class="admin"><div class="login-box">
<h1>Admin login</h1>
<?php if ($error): ?><div class="form-status error"><?= e($error) ?></div><?php endif; ?>
<form method="post" autocomplete="off"><?= csrf_field() ?>
  <div class="form-group"><label for="username">Username</label><input id="username" name="username" required autofocus></div>
  <div class="form-group"><label for="password">Password</label><input id="password" type="password" name="password" required></div>
  <button class="btn btn-primary" type="submit">Log in</button>
</form></div></body></html>
