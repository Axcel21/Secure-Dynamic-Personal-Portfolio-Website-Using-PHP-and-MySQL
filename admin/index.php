<?php
require_once __DIR__ . '/_layout.php'; require_admin();
$pdo = db();
$n = fn(string $sql) => (int)$pdo->query($sql)->fetchColumn();

/* ---- chart range: week | month | year ---- */
$range = $_GET['range'] ?? 'week';
if (!in_array($range, ['week', 'month', 'year'], true)) $range = 'week';
$today = new DateTimeImmutable('today');
$buckets = [];
if ($range === 'week') {
    for ($i = 6; $i >= 0; $i--) { $d = $today->modify("-$i day"); $buckets[$d->format('Y-m-d')] = ['l' => $d->format('D'), 'c' => 0]; }
    $start = $today->modify('-6 day');
    $keyOf = fn(DateTimeImmutable $d) => $d->format('Y-m-d');
} elseif ($range === 'month') {
    for ($w = 3; $w >= 0; $w--) $buckets["w$w"] = ['l' => 'Wk ' . (4 - $w), 'c' => 0];
    $start = $today->modify('-27 day');
    $keyOf = fn(DateTimeImmutable $d) => 'w' . intdiv((int)$d->diff($today)->days, 7);
} else {
    $first = $today->modify('first day of this month');
    for ($i = 11; $i >= 0; $i--) { $d = $first->modify("-$i month"); $buckets[$d->format('Y-m')] = ['l' => $d->format('M'), 'c' => 0]; }
    $start = $first->modify('-11 month');
    $keyOf = fn(DateTimeImmutable $d) => $d->format('Y-m');
}
$st = $pdo->prepare('SELECT created_at FROM messages WHERE created_at >= ?');
$st->execute([$start->format('Y-m-d 00:00:00')]);
foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $ts) {
    $d = (new DateTimeImmutable($ts))->setTime(0, 0);
    if ($d > $today) continue;
    $k = $keyOf($d);
    if (isset($buckets[$k])) $buckets[$k]['c']++;
}
$counts = array_column($buckets, 'c'); $labels = array_column($buckets, 'l');
$sumMsgs = array_sum($counts); $maxC = max($counts); $top = max(4, (int)(ceil($maxC / 4) * 4));
$W = 640; $H = 320; $L = 44; $R = 16; $T = 36; $B = 36;
$pw = $W - $L - $R; $ph = $H - $T - $B; $cnt = count($counts); $slot = $pw / $cnt; $bw = $slot * 0.6;
$peak = $maxC > 0 ? array_search($maxC, $counts, true) : -1;

/* ---- stats ---- */
$projects = $n('SELECT COUNT(*) FROM projects');
$skills   = $n('SELECT COUNT(*) FROM skills');
$messages = $n('SELECT COUNT(*) FROM messages');
$unread   = $n('SELECT COUNT(*) FROM messages WHERE is_read = 0');
$exp      = $n("SELECT COUNT(*) FROM resume_items WHERE section IN ('Experience','Training','Certifications','Achievements')");
$edu      = $n("SELECT COUNT(*) FROM resume_items WHERE section = 'Education'");
$recent   = $pdo->query('SELECT id, name, email, subject, is_read, created_at FROM messages ORDER BY id DESC LIMIT 6')->fetchAll();
$rangeLabel = ['week' => 'This week', 'month' => 'This month', 'year' => 'This year'];

admin_head('Dashboard', false); ?>
<div class="dash-top"><span>Showing :</span>
  <?php foreach ($rangeLabel as $k => $lab): ?><a class="seg <?= $range === $k ? 'on' : '' ?>" href="<?= e(url('admin/index.php?range=' . $k)) ?>"><?= e($lab) ?></a><?php endforeach; ?>
</div>

<div class="dash-row">
  <section class="card chart-card">
    <h2 class="h-small">Messages received · <?= e($rangeLabel[$range]) ?> (<?= $sumMsgs ?>)</h2>
    <svg viewBox="0 0 <?= $W ?> <?= $H ?>" role="img" aria-label="Bar chart of messages received">
      <?php for ($t = 0; $t <= 4; $t++): $val = (int)($top / 4 * $t); $y = round($T + $ph - ($ph * $t / 4), 1); ?>
        <line x1="<?= $L ?>" x2="<?= $W - $R ?>" y1="<?= $y ?>" y2="<?= $y ?>" class="grid"/>
        <text x="<?= $L - 8 ?>" y="<?= $y + 4 ?>" text-anchor="end" class="ylab"><?= $val ?></text>
      <?php endfor; ?>
      <?php foreach ($counts as $i => $c):
          $bh = $top ? $c / $top * $ph : 0; $x = round($L + $slot * $i + ($slot - $bw) / 2, 1); $y = round($T + $ph - $bh, 1); $cx = round($x + $bw / 2, 1); ?>
        <?php if ($c > 0): ?><rect x="<?= $x ?>" y="<?= $y ?>" width="<?= round($bw, 1) ?>" height="<?= round($bh, 1) ?>" rx="6" fill="#ff0000"/><?php endif; ?>
        <text x="<?= $cx ?>" y="<?= $H - 12 ?>" text-anchor="middle" class="xlab"><?= e($labels[$i]) ?></text>
        <?php if ($i === $peak): ?>
          <rect x="<?= $cx - 22 ?>" y="<?= max(2, $y - 34) ?>" width="44" height="22" fill="#2a00ff"/>
          <polygon points="<?= $cx - 6 ?>,<?= max(2, $y - 34) + 22 ?> <?= $cx + 6 ?>,<?= max(2, $y - 34) + 22 ?> <?= $cx ?>,<?= max(2, $y - 34) + 30 ?>" fill="#2a00ff"/>
          <text x="<?= $cx ?>" y="<?= max(2, $y - 34) + 15 ?>" text-anchor="middle" class="tip"><?= $c ?></text>
          <circle cx="<?= $cx ?>" cy="<?= $y ?>" r="6" fill="#2a00ff"/>
        <?php endif; ?>
      <?php endforeach; ?>
    </svg>
  </section>

  <div class="side-cards">
    <section class="card"><span class="mini">Total</span><strong class="mini-b">Projects</strong><div class="big"><?= $projects ?></div></section>
    <section class="card">
      <strong class="mini-b">Overview</strong>
      <table class="kv-table">
        <tr><th>Name</th><th>Total</th></tr>
        <tr><td>Skills</td><td><?= $skills ?></td></tr>
        <tr><td>Messages</td><td><?= $messages ?></td></tr>
        <tr><td>Unread messages</td><td><?= $unread ?></td></tr>
        <tr><td>Experience &amp; achievements</td><td><?= $exp ?></td></tr>
        <tr><td>Education</td><td><?= $edu ?></td></tr>
      </table>
    </section>
  </div>
</div>

<section class="card table-card">
  <div class="table-head">
    <div><strong class="mini-b">Recent messages</strong><br><em>Easily handle all your messages right here.</em></div>
    <a class="btn-dl" href="<?= e(url('admin/export_messages.php')) ?>">⬇ Download CSV</a>
  </div>
  <div class="table-scroll">
  <table class="tbl">
    <thead><tr><th>ID</th><th>Date</th><th>Subject</th><th>From</th><th>Email</th><th>Status</th></tr></thead>
    <tbody>
    <?php foreach ($recent as $m): ?>
      <tr><td><?= (int)$m['id'] ?></td><td><?= e(substr($m['created_at'], 0, 10)) ?></td><td><?= e($m['subject']) ?></td><td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td>
        <td class="<?= $m['is_read'] ? 'paid' : 'pending' ?>"><?= $m['is_read'] ? 'Read' : 'Unread' ?></td></tr>
    <?php endforeach; if (!$recent): ?><tr><td colspan="6">No messages yet.</td></tr><?php endif; ?>
    </tbody>
  </table></div>
  <p><a href="<?= e(url('admin/messages.php')) ?>">View all messages →</a></p>
</section>
<?php admin_foot();
