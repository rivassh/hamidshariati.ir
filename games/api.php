<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$queueDir = __DIR__ . '/queue';

if (!is_dir($queueDir)) {
    mkdir($queueDir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['games']) || empty($input['description'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }
    
    $request = [
        'id' => uniqid('req_', true),
        'timestamp' => date('c'),
        'games' => $input['games'],
        'changeType' => $input['changeType'] ?? 'feature',
        'description' => $input['description'],
        'priority' => $input['priority'] ?? 'medium',
        'notes' => $input['notes'] ?? '',
        'status' => 'pending'
    ];
    
    $filename = $queueDir . '/' . $request['id'] . '.json';
    file_put_contents($filename, json_encode($request, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode(['success' => true, 'id' => $request['id']]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $files = glob($queueDir . '/req_*.json');
    $requests = [];
    
    foreach ($files as $file) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            $requests[] = $data;
        }
    }
    
    usort($requests, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
    
    echo json_encode($requests);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
