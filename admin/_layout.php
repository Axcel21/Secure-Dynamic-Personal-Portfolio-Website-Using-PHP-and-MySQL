<?php
require_once __DIR__ . '/../includes/functions.php';

function admin_head(string $title, bool $showTitle = true): void {
    $f = flash();
    $cur = basename($_SERVER['SCRIPT_NAME']);
    $unread = (int)db()->query('SELECT COUNT(*) FROM messages WHERE is_read = 0')->fetchColumn();
    $S = settings();
    $words = preg_split('/\s+/', trim($S['short_name'])) ?: ['Admin'];
    $first = array_shift($words) ?: 'Admin';
    $initials = mb_strtoupper(mb_substr($S['short_name'], 0, 1) . (mb_substr(strrchr(' ' . $S['short_name'], ' '), 1, 1) ?: ''));
    $links = [
        'index.php'    => ['▦',  'Dashboard'],
        'messages.php' => ['✉️', 'Messages'],
        'projects.php' => ['🗂️', 'Projects'],
        'skills.php'   => ['🛠️', 'Skills'],
        'resume.php'   => ['📄', 'Resume & achievements'],
        'settings.php' => ['⚙️', 'Site settings'],
        'profile.php'  => ['👤', 'My account'],
    ]; ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> | Admin</title>
<link rel="stylesheet" href="<?= e(url('css/style.css')) ?>"><script src="<?= e(url('js/theme.js')) ?>"></script></head>
<body class="admin"><div class="admin-shell">
<aside class="admin-side">
  <?php foreach ($links as $file => [$icon, $label]): ?>
    <a class="<?= $cur === $file ? 'active' : '' ?>" href="<?= e(url('admin/' . $file)) ?>"><span class="ico"><?= $icon ?></span> <?= e($label) ?>
      <?php if ($file === 'messages.php' && $unread): ?><span class="badge"><?= $unread ?></span><?php endif; ?></a>
  <?php endforeach; ?>
  <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener"><span class="ico">🌐</span> View website</a>
  <div class="admin-side-foot">
    <button class="theme-toggle" data-theme-toggle type="button"><span class="icon-light">🌙 Dark mode</span><span class="icon-dark">☀️ Light mode</span></button>
    <form method="post" action="<?= e(url('admin/logout.php')) ?>"><?= csrf_field() ?><button class="btn-logout" type="submit">⎋ Logout</button></form>
  </div>
</aside>
<div class="admin-body">
<header class="admin-top">
  <span class="logo"><?= e($first) ?><b><?= e(implode(' ', $words)) ?></b></span>
  <span class="welcome">👋 Welcome Back, <?= e($first) ?> <span class="avatar"><?= e($initials) ?></span></span>
</header>
<main class="admin-main">
<?php if ($showTitle): ?><h1><?= e($title) ?></h1><?php endif; ?>
<?php if ($f): ?><div class="form-status <?= $f[1] === 'ok' ? 'success' : 'error' ?>"><?= e($f[0]) ?></div><?php endif;
}
function admin_foot(): void { ?>
</main></div></div>
<script src="<?= e(url('js/script.js')) ?>"></script>
</body></html>
<?php }
