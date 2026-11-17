<?php
require_once __DIR__ . '/_layout.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($a === 'delete') { db()->prepare('DELETE FROM skills WHERE id=?')->execute([$id]); flash('Skill deleted.'); }
    else {
        $name = trim((string)($_POST['name'] ?? '')); $note = trim((string)($_POST['note'] ?? ''));
        $level = max(0, min(100, (int)($_POST['level'] ?? 0))); $sort = (int)($_POST['sort_order'] ?? 0);
        if ($name === '' || mb_strlen($name) > 80 || mb_strlen($note) > 255) { flash('Invalid input.', 'err'); }
        elseif ($a === 'add') { db()->prepare('INSERT INTO skills (name,level,note,sort_order) VALUES (?,?,?,?)')->execute([$name,$level,$note,$sort]); flash('Skill added.'); }
        elseif ($a === 'update') { db()->prepare('UPDATE skills SET name=?,level=?,note=?,sort_order=? WHERE id=?')->execute([$name,$level,$note,$sort,$id]); flash('Skill updated.'); }
    }
    redirect('admin/skills.php');
}
$rows = db()->query('SELECT * FROM skills ORDER BY sort_order, id')->fetchAll();
admin_head('Skills'); ?>
<?php foreach (array_merge($rows, [['id'=>0,'name'=>'','level'=>0,'note'=>'','sort_order'=>count($rows)+1]]) as $r): ?>
<form method="post" class="card admin-row"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
  <input name="name" value="<?= e($r['name']) ?>" placeholder="Skill name" maxlength="80" required>
  <input name="level" type="number" min="0" max="100" value="<?= (int)$r['level'] ?>" title="Level %">
  <input name="note" value="<?= e($r['note']) ?>" placeholder="Short note" maxlength="255">
  <input name="sort_order" type="number" value="<?= (int)$r['sort_order'] ?>" title="Order">
  <?php if ($r['id']): ?>
    <button class="btn btn-primary" name="action" value="update">Save</button>
    <button class="btn btn-outline" name="action" value="delete" data-confirm="Delete this skill?">Delete</button>
  <?php else: ?><button class="btn btn-primary" name="action" value="add">Add skill</button><?php endif; ?>
</form>
<?php endforeach; admin_foot();
