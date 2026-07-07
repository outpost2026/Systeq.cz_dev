<?php
error_reporting(0);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('X-Robots-Tag: noindex');

$baseDir = __DIR__;
$configPath = $baseDir . '/config.json';
$dataDir = $baseDir . '/data';
$visitorsPath = $dataDir . '/visitors.json';
$logPath = $dataDir . '/visitor_log.jsonl';

// ---- init dirs ----
if (!is_dir($dataDir)) mkdir($dataDir, 0755, true);

// ---- load config ----
$config = json_decode(file_get_contents($configPath), true) ?: [];
$excludedIps = $config['excluded_ips'] ?? [];
$excludedRanges = $config['excluded_ip_ranges'] ?? [];
$excludedUas = $config['excluded_user_agents'] ?? [];
$salt = $config['hash_salt'] ?? 'default_salt';
$windowHours = $config['unique_window_hours'] ?? 24;
$logUa = $config['log_user_agent'] ?? false;
$logRef = $config['log_referrer'] ?? true;

$action = $_GET['action'] ?? '';
$token = $_GET['token'] ?? '';

// ---- action: set skip cookie ----
if ($action === 'skip_me' && $token !== '' && $token === ($config['skip_token'] ?? '')) {
    setcookie('systeq_skip_counter', '1', time() + 315360000, '/', '', false, true);
    http_response_code(204);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
$referrer = $_GET['ref'] ?? '';           // from JS document.referrer
$page = $_GET['page'] ?? '';              // from JS location.pathname

// ---- skip check: cookie ----
if (isset($_COOKIE['systeq_skip_counter']) && $_COOKIE['systeq_skip_counter'] === '1') {
    http_response_code(204);
    exit;
}

// ---- skip check: excluded IP ranges ----
if ($ip !== '') {
    foreach ($excludedIps as $e) {
        if ($ip === $e) {
            http_response_code(204);
            exit;
        }
    }
    $ipLong = ip2long($ip);
    if ($ipLong !== false) {
        foreach ($excludedRanges as $range) {
            if (ipInRange($ip, $range)) {
                http_response_code(204);
                exit;
            }
        }
    }
}

// ---- skip check: excluded user agents ----
if ($ua !== '') {
    foreach ($excludedUas as $e) {
        if (stripos($ua, $e) !== false) {
            http_response_code(204);
            exit;
        }
    }
}

// ---- hash IP for privacy ----
$ipHash = hash('sha256', $salt . $ip . date('Y-m-d'));

// ---- dedup: check daily visitors ----
$visitors = [];
if (file_exists($visitorsPath)) {
    $visitors = json_decode(file_get_contents($visitorsPath), true) ?: [];
}
$today = date('Y-m-d');
if (!isset($visitors[$today])) $visitors[$today] = [];

$isNew = false;
$nowTs = time();
$recentHashes = $visitors[$today];
$found = false;
foreach ($recentHashes as $entry) {
    if ($entry['hash'] === $ipHash) {
        if (($nowTs - $entry['ts']) < $windowHours * 3600) {
            $found = true;
        }
        break;
    }
}
if (!$found) {
    $visitors[$today][] = ['hash' => $ipHash, 'ts' => $nowTs];
    $isNew = true;
}

// ---- count unique visitors across all days ----
$totalUnique = 0;
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

// ---- cleanup: keep only last 90 days ----
$cutoff = date('Y-m-d', strtotime('-90 days'));
foreach ($visitors as $day => $entries) {
    if ($day < $cutoff) unset($visitors[$day]);
}
file_put_contents($visitorsPath, json_encode($visitors, JSON_PRETTY_PRINT), LOCK_EX);

// ---- log visit if new ----
if ($isNew) {
    $logEntry = [
        'ts' => date('c'),
        'ip_hash' => $ipHash,
        'referrer' => $logRef ? $referrer : '',
        'ua' => $logUa ? $ua : '',
        'page' => $page,
    ];
    file_put_contents($logPath, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

// ---- return count ----
echo json_encode(['count' => $totalUnique]);

// ---- helper ----
function ipInRange($ip, $range): bool {
    if (strpos($range, '/') === false) return $ip === $range;
    [$subnet, $bits] = explode('/', $range);
    $bits = (int)$bits;
    if ($bits === 0) return true;
    $ipLong = ip2long($ip);
    $subnetLong = ip2long($subnet);
    if ($ipLong === false || $subnetLong === false) return false;
    $mask = -1 << (32 - $bits);
    return ($ipLong & $mask) === ($subnetLong & $mask);
}
