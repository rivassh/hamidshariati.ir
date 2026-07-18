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

$message = $_POST['message'] ?? '';
$games = json_decode($_POST['games'] ?? '["snake"]', true);
$file = $_FILES['file'] ?? null;

if (empty($message) && !$file) {
    http_response_code(400);
    echo json_encode(['error' => 'Message or file is required']);
    exit;
}

$fileInfo = '';
if ($file && $file['error'] === UPLOAD_ERR_OK) {
    $fileContent = file_get_contents($file['tmp_name']);
    $fileInfo = "\n\nفایل ضمیمه: " . $file['name'] . ' (' . round($file['size'] / 1024, 1) . ' KB)';
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (in_array($ext, ['js', 'html', 'css', 'json', 'txt'])) {
        $fileInfo .= "\nمحتوای فایل:\n```\n" . $fileContent . "\n```";
    }
}

$fullMessage = $message . $fileInfo;

$apiUrl = 'https://hamidshariati.ir/games/mimo-api/chat';
$payload = json_encode([
    'message' => $fullMessage,
    'games' => $games
]);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 40);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    echo json_encode([
        'response' => 'خطا در اتصال به mimo API: ' . $curlError,
        'status' => 'error'
    ]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode([
        'response' => 'mimo API پاسخ نامعتبر برگرداند.',
        'status' => 'error'
    ]);
    exit;
}

echo $response;
