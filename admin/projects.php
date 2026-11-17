<?php
require_once __DIR__ . '/_layout.php'; require_admin();

function save_upload(): ?string {
    if (empty($_FILES['image']['name'])) return null;
    $f = $_FILES['image'];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) throw new RuntimeException('Image must be under 2 MB.');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime] ?? null;
    if (!$ext || !getimagesize($f['tmp_name'])) throw new RuntimeException('Only JPG, PNG or WebP images are allowed.');
    $name = bin2hex(random_bytes(12)) . '.' . $ext; // random name: never trust the client filename
    if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../uploads/' . $name)) throw new RuntimeException('Upload failed.');
    return 'uploads/' . $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $a = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($a === 'delete') { db()->prepare('DELETE FROM projects WHERE id=?')->execute([$id]); flash('Project deleted.'); }
        else {
            $t = trim((string)($_POST['title'] ?? '')); $d = trim((string)($_POST['description'] ?? ''));
            $tag = trim((string)($_POST['tag'] ?? '')); $tools = trim((string)($_POST['tools'] ?? ''));
            if ($t === '' || $d === '' || $tag === '' || mb_strlen($t) > 150 || mb_strlen($tag) > 50 || mb_strlen($tools) > 150) throw new RuntimeException('Please fill title, description and tag correctly.');
            $img = save_upload();
            if ($a === 'add') {
                db()->prepare('INSERT INTO projects (title,description,tag,tools,image) VALUES (?,?,?,?,?)')->execute([$t,$d,$tag,$tools,$img ?? '']); flash('Project added.');
            } elseif ($a === 'update') {
                if ($img) db()->prepare('UPDATE projects SET title=?,description=?,tag=?,tools=?,image=? WHERE id=?')->execute([$t,$d,$tag,$tools,$img,$id]);
                else      db()->prepare('UPDATE projects SET title=?,description=?,tag=?,tools=? WHERE id=?')->execute([$t,$d,$tag,$tools,$id]);
                flash('Project updated.');
            }
        }
    } catch (RuntimeException $ex) { flash($ex->getMessage(), 'err'); }
    redirect('admin/projects.php');
}
$rows = db()->query('SELECT * FROM projects ORDER BY id DESC')->fetchAll();
admin_head('Projects');
foreach (array_merge([['id'=>0,'title'=>'','description'=>'','tag'=>'','tools'=>'','image'=>'']], $rows) as $r): ?>
<form method="post" enctype="multipart/form-data" class="card admin-col"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
  <input name="title" value="<?= e($r['title']) ?>" placeholder="Title" maxlength="150" required>
  <textarea name="description" placeholder="Description" required><?= e($r['description']) ?></textarea>
  <input name="tag" value="<?= e($r['tag']) ?>" placeholder="Filter tag (e.g. Cisco)" maxlength="50" required>
  <input name="tools" value="<?= e($r['tools']) ?>" placeholder="Tools, comma separated" maxlength="150">
  <label>Image (JPG/PNG/WebP, max 2 MB)<?= $r['image'] ? ' – current: ' . e($r['image']) : '' ?> <input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>
  <?php if ($r['id']): ?>
    <div><button class="btn btn-primary" name="action" value="update">Save</button>
    <button class="btn btn-outline" name="action" value="delete" data-confirm="Delete this project?">Delete</button></div>
  <?php else: ?><div><button class="btn btn-primary" name="action" value="add">Add project</button></div><?php endif; ?>
</form>
<?php endforeach; admin_foot();
