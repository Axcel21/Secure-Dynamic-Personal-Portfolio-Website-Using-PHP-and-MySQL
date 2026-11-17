<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="messages-' . date('Y-m-d') . '.csv"');
$out = fopen('php://output', 'w');
fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM so Excel reads accents correctly
fputcsv($out, ['ID', 'Date', 'Name', 'Email', 'Subject', 'Message', 'Status']);
// Neutralise spreadsheet formula injection: cells that start with = + - @ are prefixed with '
$safe = fn($v) => preg_match('/^[=+\-@\t\r]/', (string)$v) ? "'" . $v : $v;
foreach (db()->query('SELECT * FROM messages ORDER BY id DESC') as $m) {
    fputcsv($out, [$m['id'], $m['created_at'], $safe($m['name']), $safe($m['email']), $safe($m['subject']), $safe($m['message']), $m['is_read'] ? 'Read' : 'Unread']);
}
fclose($out);
