<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['error' => 'Method not allowed']); exit; }

if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No audio file uploaded']);
    exit;
}

$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    $envFile = '/root/.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (preg_match('/^OPENAI_API_KEY\s*=\s*(.+)$/', $line, $m)) {
                $apiKey = trim($m[1]);
                break;
            }
        }
    }
}

if (!$apiKey) {
    echo json_encode(['error' => 'OPENAI_API_KEY not set. Add it to /root/.env']);
    exit;
}

$tmpFile = $_FILES['audio']['tmp_name'];
$originalName = $_FILES['audio']['name'];
$ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

$mimeMap = [
    'mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'm4a' => 'audio/mp4',
    'webm' => 'audio/webm', 'mp4' => 'audio/mp4', 'ogg' => 'audio/ogg',
    'flac' => 'audio/flac',
];
$mime = $mimeMap[$ext] ?? 'audio/mpeg';

$ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $apiKey],
    CURLOPT_POSTFIELDS => [
        'file' => new CURLFile($tmpFile, $mime, $originalName),
        'model' => 'whisper-1',
        'language' => 'fa',
    ],
    CURLOPT_TIMEOUT => 60,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo json_encode(['error' => 'cURL error: ' . $err]);
    exit;
}

if ($httpCode !== 200) {
    echo json_encode(['error' => 'API error (HTTP ' . $httpCode . '): ' . $response]);
    exit;
}

$result = json_decode($response, true);
echo json_encode(['text' => $result['text'] ?? '']);
