<?php $title = 'Home'; $wide = true; require __DIR__ . '/includes/header.php'; ?>
<section class="hero">
  <div>
    <h1>Hi, I'm <?= e($S['short_name']) ?>.</h1>
    <p class="role-line">I'm a <span data-typing='<?= e(json_encode([$S['role']])) ?>'></span></p>
    <p class="intro"><?= e($S['hero_intro']) ?></p>
    <div class="cta-row">
      <a class="btn btn-primary" href="<?= e(url('projects.php')) ?>">View my projects</a>
      <a class="btn btn-outline" href="<?= e(url('contact.php')) ?>">Get in touch</a>
    </div>
  </div>
  <div class="hero-figure"><img src="<?= e(url('images/facebook.jpg')) ?>" alt="Portrait of <?= e($S['short_name']) ?>"></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
