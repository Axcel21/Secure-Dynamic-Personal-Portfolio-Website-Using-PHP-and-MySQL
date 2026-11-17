<?php $title = 'Projects'; $wide = true; require __DIR__ . '/includes/header.php';
$projects = db()->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();
$tags = array_values(array_unique(array_column($projects, 'tag'))); sort($tags); ?>
<div class="page-header"><h1>Projects</h1>
  <p class="section-lead">My projects showcase my knowledge and skills in Information Technology through the use of different software and tools.</p></div>
<div class="filter-row" role="group" aria-label="Filter projects by technology">
  <button class="filter-btn is-active" data-filter="all" type="button">All</button>
  <?php foreach ($tags as $t): ?><button class="filter-btn" data-filter="<?= e($t) ?>" type="button"><?= e($t) ?></button><?php endforeach; ?>
</div>
<div class="project-grid">
  <?php foreach ($projects as $p): ?>
  <article class="project-card" data-project-tags="<?= e($p['tag']) ?>">
    <div class="project-thumb" data-modal-trigger data-caption="<?= e($p['title']) ?>">
      <?php if ($p['image']): ?><img src="<?= e(url($p['image'])) ?>" alt="Screenshot of <?= e($p['title']) ?>"><?php endif; ?>
    </div>
    <div class="project-body">
      <h3><?= e($p['title']) ?></h3>
      <p><?= e($p['description']) ?></p>
      <div class="project-links"><?php foreach (array_filter(array_map('trim', explode(',', $p['tools']))) as $tool): ?><span><?= e($tool) ?></span><?php endforeach; ?></div>
    </div>
  </article>
  <?php endforeach; ?>
</div>
<div class="modal-overlay" data-modal-overlay>
  <div class="modal-box"><img data-modal-image src="" alt="">
    <div class="modal-caption"><span data-modal-caption></span><button class="modal-close" data-modal-close aria-label="Close preview">✕</button></div></div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
