<?php
error_reporting(0);
header('X-Robots-Tag: noindex');

$baseDir = __DIR__;
$logPath = $baseDir . '/data/visitor_log.jsonl';
$visitorsPath = $baseDir . '/data/visitors.json';

// ---- load data ----
$logs = [];
if (file_exists($logPath)) {
    $lines = file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $logs[] = json_decode($line, true);
    }
    $logs = array_reverse($logs);
}

$visitors = [];
$totalUnique = 0;
if (file_exists($visitorsPath)) {
    $visitors = json_decode(file_get_contents($visitorsPath), true) ?: [];
    $seen = [];
    foreach ($visitors as $day => $entries) {
        foreach ($entries as $entry) {
            $h = $entry['hash'];
            if (!isset($seen[$h])) {
                $seen[$h] = true;
                $totalUnique++;
            }
        }
    }
}

// ---- stats ----
$today = date('Y-m-d');
$todayCount = count($visitors[$today] ?? []);
$referrers = [];
foreach ($logs as $log) {
    $ref = $log['referrer'] ?? '(direct)';
    if ($ref === '') $ref = '(direct)';
    $parsed = parse_url($ref, PHP_URL_HOST) ?: '(direct)';
    $referrers[$parsed] = ($referrers[$parsed] ?? 0) + 1;
}
arsort($referrers);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SYSTEQ — Visitor Report</title>
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:system-ui,-apple-system,sans-serif;background:#0e1320;color:#e2e8f0;padding:2rem;font-size:14px;line-height:1.6}
h1{color:#0d9488;font-size:1rem;font-weight:600;letter-spacing:.12em;margin-bottom:1.5rem;text-transform:uppercase}
h2{color:#e2e8f0;font-size:.8rem;font-weight:600;letter-spacing:.08em;margin:1.5rem 0 .5rem;text-transform:uppercase}
.stats{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem}
.stat-card{background:#131926;border:1px solid rgba(255,255,255,.08);border-radius:4px;padding:.75rem 1rem;min-width:120px}
.stat-num{font-size:1.5rem;font-weight:700;color:#0d9488}
.stat-label{font-size:.65rem;color:#64748b;text-transform:uppercase;letter-spacing:.08em}
table{width:100%;border-collapse:collapse;background:#131926;border:1px solid rgba(255,255,255,.08);border-radius:4px;overflow:hidden;font-size:.8rem}
th{background:#0c1018;color:#64748b;text-transform:uppercase;letter-spacing:.08em;font-size:.65rem;padding:.5rem .75rem;text-align:left;border-bottom:1px solid rgba(255,255,255,.08)}
td{padding:.4rem .75rem;border-bottom:1px solid rgba(255,255,255,.04);color:#c8d0dc}
tr:hover td{background:rgba(255,255,255,.02)}
.muted{color:#64748b;font-size:.75rem}
.footer{margin-top:2rem;font-size:.65rem;color:#2e3d52;letter-spacing:.05em}
a{color:#0d9488;text-decoration:none}
a:hover{text-decoration:underline}
</style>
</head>
<body>

<h1>SYSTEQ // Visitor Report</h1>

<div class="stats">
  <div class="stat-card">
    <div class="stat-num"><?= number_format($totalUnique) ?></div>
    <div class="stat-label">Total Unique</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= number_format($todayCount) ?></div>
    <div class="stat-label">Today Unique</div>
  </div>
  <div class="stat-card">
    <div class="stat-num"><?= number_format(count($logs)) ?></div>
    <div class="stat-label">All Visits (90d)</div>
  </div>
</div>

<h2>Referrers</h2>
<table>
<tr><th>Source</th><th>Visits</th></tr>
<?php $i = 0; foreach ($referrers as $src => $n): ?>
<tr<?= $i++ % 2 ? ' style="background:rgba(255,255,255,.02)"' : '' ?>>
  <td><?= htmlspecialchars($src) ?></td>
  <td><?= $n ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Recent Visits (last 50)</h2>
<table>
<tr><th>Time</th><th>IP Hash</th><th>Referrer</th></tr>
<?php $i = 0; foreach (array_slice($logs, 0, 50) as $log): ?>
<tr<?= $i++ % 2 ? ' style="background:rgba(255,255,255,.02)"' : '' ?>>
  <td class="muted"><?= htmlspecialchars($log['ts'] ?? '') ?></td>
  <td class="muted" style="font-family:monospace;font-size:.7rem"><?= substr(htmlspecialchars($log['ip_hash'] ?? ''), 0, 16) ?>…</td>
  <td style="max-width:400px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= htmlspecialchars($log['referrer'] ?? '') ?: '<span class="muted">(direct)</span>' ?></td>
</tr>
<?php endforeach; ?>
</table>

<div class="footer">SYSTEQ Engine v20 &middot; data stored locally</div>

</body>
</html>
