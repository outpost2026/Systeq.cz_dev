<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('X-Robots-Tag: noindex');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $contact === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Vyplňte prosím jméno a kontakt.']);
    exit;
}

$serviceLabels = [
    'zahrada' => 'Zahradní práce', 'offgrid' => 'Offgrid & FVE',
    'drevo' => 'Dřevěné konstrukce', 'iot' => 'IoT & dálková správa',
    'zabezpeceni' => 'Zabezpečení', 'jine' => 'Jiné / nevím',
];
$serviceLabel = $serviceLabels[$service] ?? '—';

$body = "Jméno: $name\nKontakt: $contact\nOblast: $serviceLabel\n\nZpráva:\n$message\n";
$bodySafe = iconv('UTF-8', 'UTF-8//IGNORE', $body);

$to      = 'sousek@systeq.cz';
$subject = '=?UTF-8?B?' . base64_encode("Poptávka z webu — $name") . '?=';
$headers = "From: $name <noreply@systeq.cz>\r\n"
         . "Reply-To: $contact\r\n"
         . "MIME-Version: 1.0\r\n"
         . "Content-Type: text/plain; charset=utf-8\r\n"
         . "X-Mailer: PHP/" . phpversion();

$logDir = __DIR__ . '/data';
if (!is_dir($logDir)) mkdir($logDir, 0755, true);
$logEntry = json_encode([
    'time' => date('Y-m-d H:i:s'), 'name' => $name,
    'contact' => $contact, 'service' => $serviceLabel, 'message' => $message,
], JSON_UNESCAPED_UNICODE) . "\n";
file_put_contents($logDir . '/poptavky.jsonl', $logEntry, FILE_APPEND | LOCK_EX);

$ok = mail($to, $subject, $bodySafe, $headers);

if ($ok) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Odeslání selhalo. Zkuste to prosím později nebo zavolejte na 735 045 256.']);
}
