<?php
require_once __DIR__ . '/_layout.php'; require_admin();
$fields = ['short_name'=>'Display name','role'=>'Role','email'=>'Email','github'=>'GitHub URL','location'=>'Location',
           'hero_intro'=>'Home intro','about_lead'=>'About – lead','about_background'=>'About – background',
           'about_education'=>'About – education','interests'=>'Interests (comma separated)'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $ok = true;
    $email = trim((string)($_POST['email'] ?? '')); $gh = trim((string)($_POST['github'] ?? ''));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { flash('Invalid email.', 'err'); $ok = false; }
    elseif (!preg_match('#^https://#i', $gh) || !filter_var($gh, FILTER_VALIDATE_URL)) { flash('GitHub URL must start with https://', 'err'); $ok = false; }
    if ($ok) {
        $st = db()->prepare('INSERT INTO settings (`key`,`value`) VALUES (?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        foreach ($fields as $k => $_) $st->execute([$k, trim((string)($_POST[$k] ?? ''))]);
        flash('Profile saved.');
    }
    redirect('admin/settings.php');
}
$S = settings(); admin_head('Profile'); ?>
<form method="post" class="card admin-col"><?= csrf_field() ?>
<?php foreach ($fields as $k => $label): $long = str_starts_with($k, 'hero') || str_starts_with($k, 'about'); ?>
  <label><?= e($label) ?>
  <?php if ($long): ?><textarea name="<?= $k ?>" required><?= e($S[$k] ?? '') ?></textarea>
  <?php else: ?><input name="<?= $k ?>" value="<?= e($S[$k] ?? '') ?>" maxlength="255" required><?php endif; ?></label>
<?php endforeach; ?>
  <div><button class="btn btn-primary" type="submit">Save profile</button></div>
</form>
<?php admin_foot();
