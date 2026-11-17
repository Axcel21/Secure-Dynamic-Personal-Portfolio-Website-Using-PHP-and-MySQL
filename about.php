<?php $title = 'About'; require __DIR__ . '/includes/header.php'; ?>
<div class="page-header"><h1>About me</h1><p class="section-lead"><?= e($S['about_lead']) ?></p></div>
<section class="section about-grid">
  <div><h2>Background</h2><p><?= e($S['about_background']) ?></p></div>
  <div><h2>Education</h2><p><?= e($S['about_education']) ?></p></div>
</section>
<section class="section"><h2>Interests</h2>
  <div class="interest-tags">
    <?php foreach (array_filter(array_map('trim', explode(',', $S['interests']))) as $i): ?><span><?= e($i) ?></span><?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
