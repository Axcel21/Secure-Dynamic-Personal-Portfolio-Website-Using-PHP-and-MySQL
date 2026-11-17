<?php
require_once __DIR__ . '/_layout.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $id = (int)($_POST['id'] ?? 0); $a = $_POST['action'] ?? '';
    if ($a === 'delete')   { db()->prepare('DELETE FROM messages WHERE id=?')->execute([$id]); flash('Message deleted.'); }
    if ($a === 'read')     { db()->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([$id]); flash('Marked as read.'); }
    if ($a === 'read_all') { db()->exec('UPDATE messages SET is_read=1'); flash('All messages marked as read.'); }
    redirect('admin/messages.php' . (isset($_GET['unread']) ? '?unread=1' : ''));
}
$onlyUnread = isset($_GET['unread']);
$msgs = db()->query('SELECT * FROM messages ' . ($onlyUnread ? 'WHERE is_read = 0 ' : '') . 'ORDER BY id DESC LIMIT 200')->fetchAll();
admin_head('Messages'); ?>
<div class="toolbar">
  <a class="btn <?= $onlyUnread ? 'btn-outline' : 'btn-primary' ?>" href="<?= e(url('admin/messages.php')) ?>">All</a>
  <a class="btn <?= $onlyUnread ? 'btn-primary' : 'btn-outline' ?>" href="<?= e(url('admin/messages.php?unread=1')) ?>">Unread</a>
  <form method="post"><?= csrf_field() ?><button class="btn btn-outline" name="action" value="read_all">Mark all read</button></form>
</div>
<?php if (!$msgs): ?><p>No messages.</p><?php endif; ?>
<?php foreach ($msgs as $m): ?>
<div class="card msg <?= $m['is_read'] ? '' : 'unread' ?>">
  <h3><?= e($m['subject']) ?></h3>
  <p class="skill-note"><?= e($m['name']) ?> &lt;<a href="mailto:<?= e($m['email']) ?>"><?= e($m['email']) ?></a>&gt; · <?= e($m['created_at']) ?></p>
  <p class="pre"><?= e($m['message']) ?></p>
  <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
    <?php if (!$m['is_read']): ?><button class="btn btn-outline" name="action" value="read">Mark read</button><?php endif; ?>
    <button class="btn btn-outline" name="action" value="delete" data-confirm="Delete this message?">Delete</button></form>
</div>
<?php endforeach; admin_foot();
