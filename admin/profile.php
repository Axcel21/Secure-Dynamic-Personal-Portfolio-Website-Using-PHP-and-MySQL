<?php
require_once __DIR__ . '/_layout.php'; require_admin();
$st = db()->prepare('SELECT id, username, password_hash, created_at FROM admins WHERE id = ?');
$st->execute([$_SESSION['admin_id']]); $me = $st->fetch();
if (!$me) { $_SESSION = []; redirect('admin/login.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $cur = (string)($_POST['current'] ?? ''); $new = (string)($_POST['new'] ?? ''); $conf = (string)($_POST['confirm'] ?? '');
    if (!password_verify($cur, $me['password_hash'])) flash('Current password is incorrect.', 'err');
    elseif (strlen($new) < 10) flash('New password must be at least 10 characters.', 'err');
    elseif ($new !== $conf) flash('New password and confirmation do not match.', 'err');
    elseif ($new === $cur) flash('New password must be different from the current one.', 'err');
    else {
        db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')->execute([password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        session_regenerate_id(true);
        flash('Password changed successfully.');
    }
    redirect('admin/profile.php');
}
admin_head('My account'); ?>
<div class="card admin-col">
  <h2 class="h-small">Administrator profile</h2>
  <dl class="kv"><dt>Username</dt><dd><?= e($me['username']) ?></dd><dt>Account created</dt><dd><?= e($me['created_at']) ?></dd></dl>
</div>
<form method="post" class="card admin-col" autocomplete="off"><?= csrf_field() ?>
  <h2 class="h-small">Change password</h2>
  <label>Current password <input type="password" name="current" required></label>
  <label>New password (10+ characters) <input type="password" name="new" minlength="10" required></label>
  <label>Confirm new password <input type="password" name="confirm" minlength="10" required></label>
  <div><button class="btn btn-primary" type="submit">Update password</button></div>
</form>
<?php admin_foot();
