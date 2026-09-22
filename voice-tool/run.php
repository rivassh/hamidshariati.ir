<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }

$input = json_decode(file_get_contents('php://input'), true);
$command = trim($input['command'] ?? '');

if (empty($command)) {
    http_response_code(400);
    echo json_encode(['error' => 'No command provided']);
    exit;
}

$queueDir = '/var/www/websites/hamidshariati.ir/voice-tool/queue';
if (!is_dir($queueDir)) mkdir($queueDir, 0777, true);

$filename = $queueDir . '/cmd_' . time() . '_' . uniqid() . '.txt';
file_put_contents($filename, $command);

echo json_encode(['success' => true, 'command' => $command]);
