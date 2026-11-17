<?php
require_once __DIR__ . '/_layout.php'; require_admin();
$sections = ['Education','Experience','Training','Certifications','Achievements'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($a === 'delete') { db()->prepare('DELETE FROM resume_items WHERE id=?')->execute([$id]); flash('Entry deleted.'); }
    else {
        $sec = (string)($_POST['section'] ?? ''); $t = trim((string)($_POST['title'] ?? ''));
        $org = trim((string)($_POST['org'] ?? '')); $body = trim((string)($_POST['body'] ?? '')); $sort = (int)($_POST['sort_order'] ?? 0);
        if (!in_array($sec, $sections, true) || $body === '' || mb_strlen($t) > 150 || mb_strlen($org) > 150) flash('Please check the form: choose a section and write a description.', 'err');
        elseif ($a === 'add')    { db()->prepare('INSERT INTO resume_items (section,title,org,body,sort_order) VALUES (?,?,?,?,?)')->execute([$sec,$t,$org,$body,$sort]); flash('Entry added.'); }
        elseif ($a === 'update') { db()->prepare('UPDATE resume_items SET section=?,title=?,org=?,body=?,sort_order=? WHERE id=?')->execute([$sec,$t,$org,$body,$sort,$id]); flash('Entry updated.'); }
    }
    redirect('admin/resume.php');
}
$rows = db()->query('SELECT * FROM resume_items ORDER BY section, sort_order, id')->fetchAll();
admin_head('Resume & achievements');
foreach (array_merge([['id'=>0,'section'=>'Experience','title'=>'','org'=>'','body'=>'','sort_order'=>0]], $rows) as $r): ?>
<form method="post" class="card admin-col"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
  <strong><?= $r['id'] ? 'Edit entry' : 'Add new entry' ?></strong>
  <select name="section"><?php foreach ($sections as $s): ?><option <?= $r['section'] === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select>
  <input name="title" value="<?= e($r['title']) ?>" placeholder="Title (optional)" maxlength="150">
  <input name="org" value="<?= e($r['org']) ?>" placeholder="Organization / school (optional)" maxlength="150">
  <textarea name="body" placeholder="Description" required><?= e($r['body']) ?></textarea>
  <input name="sort_order" type="number" value="<?= (int)$r['sort_order'] ?>" title="Display order">
  <div><?php if ($r['id']): ?>
    <button class="btn btn-primary" name="action" value="update">Save</button>
    <button class="btn btn-outline" name="action" value="delete" data-confirm="Delete this entry?">Delete</button>
  <?php else: ?><button class="btn btn-primary" name="action" value="add">Add entry</button><?php endif; ?></div>
</form>
<?php endforeach; admin_foot();
