<?php
require_once __DIR__ . '/functions.php';
$S = settings();
$current = basename($_SERVER['SCRIPT_NAME']);
$pages = ['index.php'=>'Home','about.php'=>'About','skills.php'=>'Skills','projects.php'=>'Projects','resume.php'=>'Resume','contact.php'=>'Contact'];
function nav_links(array $pages, string $current): void {
    echo '<ul class="nav-list">';
    foreach ($pages as $file => $label) {
        $cls = $file === $current ? 'nav-link active' : 'nav-link';
        echo '<li><a class="' . $cls . '" href="' . e(url($file)) . '">' . e($label) . '</a></li>';
    }
    echo '</ul>';
}
function sidebar_footer(array $S): void { ?>
  <div class="sidebar-footer">
    <div class="social-row">
      <a href="<?= e($S['github']) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">GH</a>
      <a href="mailto:<?= e($S['email']) ?>" aria-label="Email">@</a>
    </div>
    <button class="theme-toggle" data-theme-toggle type="button">
      <span class="icon-light">🌙 Dark mode</span><span class="icon-dark">☀️ Light mode</span>
    </button>
  </div>
<?php } ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title ?? 'Portfolio') ?> | <?= e($S['short_name']) ?></title>
  <link rel="stylesheet" href="<?= e(url('css/style.css')) ?>">
  <script src="<?= e(url('js/theme.js')) ?>"></script>
</head>
<body>
<header class="topbar">
  <span class="brand-name"><?= e($S['short_name']) ?></span>
  <button class="hamburger" data-hamburger aria-label="Toggle navigation menu" aria-expanded="false"><span></span><span></span><span></span></button>
</header>
<nav class="mobile-drawer" data-mobile-drawer aria-label="Mobile navigation">
  <?php nav_links($pages, $current); sidebar_footer($S); ?>
</nav>
<div class="app">
  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark">AJ</div>
      <span class="brand-name"><?= e($S['short_name']) ?></span>
      <span class="brand-role"><?= e($S['role']) ?></span>
    </div>
    <nav aria-label="Primary navigation"><?php nav_links($pages, $current); ?></nav>
    <?php sidebar_footer($S); ?>
  </aside>
  <main class="main"><div class="<?= !empty($wide) ? 'content-wide' : 'content' ?>">
