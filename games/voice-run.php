<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$command = trim($input['command'] ?? '');

if (empty($command)) {
    http_response_code(400);
    echo json_encode(['error' => 'No command provided']);
    exit;
}

$session = 'voice-session';
$tmux_bin = '/usr/bin/tmux';

if (!file_exists($tmux_bin)) {
    $tmux_bin = '/usr/local/bin/tmux';
}

if (!file_exists($tmux_bin)) {
    echo json_encode(['error' => 'tmux not found']);
    exit;
}

$isAttached = (int) shell_exec("$tmux_bin has-session -t $session 2>&1 && echo 1 || echo 0");

if (!$isAttached) {
    shell_exec("$tmux_bin new-session -d -s $session");
}

$escaped = escapeshellarg($command);
shell_exec("$tmux_bin send-keys -t $session $escaped Enter");

echo json_encode(['success' => true, 'session' => $session, 'command' => $command]);
