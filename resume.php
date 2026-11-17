<?php $title = 'Resume'; require __DIR__ . '/includes/header.php';
$sections = [];
foreach (db()->query('SELECT * FROM resume_items ORDER BY sort_order, id') as $r) $sections[$r['section']][] = $r;
$order = ['Education','Experience','Training','Certifications'];
uksort($sections, fn($a,$b) => (array_search($a,$order) === false ? 99 : array_search($a,$order)) <=> (array_search($b,$order) === false ? 99 : array_search($b,$order)));
$hasPdf = is_file(__DIR__ . '/assets/resume.pdf'); ?>
<div class="page-header"><h1>Resume</h1><p class="section-lead">Click a section to expand it.</p></div>
<?php if ($hasPdf): ?><div class="resume-toolbar"><a class="btn btn-outline" href="<?= e(url('assets/resume.pdf')) ?>" download>Download PDF résumé</a></div><?php endif; ?>
<section class="section">
  <?php foreach ($sections as $name => $items): ?>
  <div class="accordion-item" data-accordion-item>
    <button class="accordion-trigger" data-accordion-trigger aria-expanded="false"><?= e($name) ?> <span class="accordion-icon">+</span></button>
    <div class="accordion-panel" data-accordion-panel><div class="accordion-panel-inner">
      <?php foreach ($items as $it): ?>
      <div class="timeline-entry">
        <?php if ($it['title']): ?><div class="timeline-head"><h4><?= e($it['title']) ?></h4></div><?php endif; ?>
        <?php if ($it['org']): ?><p class="timeline-org"><?= e($it['org']) ?></p><?php endif; ?>
        <p><?= e($it['body']) ?></p>
      </div>
      <?php endforeach; ?>
    </div></div>
  </div>
  <?php endforeach; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
