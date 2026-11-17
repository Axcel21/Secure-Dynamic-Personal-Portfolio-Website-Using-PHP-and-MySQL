<?php $title = 'Skills'; $wide = true; require __DIR__ . '/includes/header.php';
$skills = db()->query('SELECT name, level, note FROM skills ORDER BY sort_order, id')->fetchAll(); ?>
<div class="page-header"><h1>Skills</h1>
  <p class="section-lead">Bars fill in when you scroll to them. The number is a rough measure of how much I've used each tool, not a claim of mastery.</p></div>
<section class="section skills-grid">
  <?php foreach ($skills as $s): ?>
  <div class="card skill-card">
    <div class="skill-top"><h3><?= e($s['name']) ?></h3><span><?= (int)$s['level'] ?>%</span></div>
    <div class="skill-bar-track"><div class="skill-bar-fill" data-skill-level="<?= (int)$s['level'] ?>"></div></div>
    <p class="skill-note"><?= e($s['note']) ?></p>
  </div>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
