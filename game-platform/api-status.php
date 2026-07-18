<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

$gameDir = __DIR__;
$gameFile = $gameDir . '/game.js';
$styleFile = $gameDir . '/style.css';
$indexFile = $gameDir . '/index.html';

$response = [
    'status' => 'ok',
    'game' => 'snake-neon',
    'name' => 'مار نئون',
    'version' => '1.1.0',
    'engine' => 'Phaser 3.80.1',
    'timestamp' => date('c'),
    'server' => [
        'php' => PHP_VERSION,
        'uptime' => $_SERVER['REQUEST_TIME'] ?? time(),
    ],
    'assets' => [
        'index' => file_exists($indexFile),
        'game_js' => file_exists($gameFile),
        'style_css' => file_exists($styleFile),
    ],
    'game_js_size' => file_exists($gameFile) ? filesize($gameFile) : 0,
    'style_css_size' => file_exists($styleFile) ? filesize($styleFile) : 0,
];

if (isset($_GET['health'])) {
    $allAssetsOk = $response['assets']['index'] && $response['assets']['game_js'] && $response['assets']['style_css'];
    $response['health'] = $allAssetsOk ? 'healthy' : 'degraded';
    if (!$allAssetsOk) {
        $response['status'] = 'warning';
        $response['missing'] = array_keys(array_filter($response['assets'], fn($v) => !$v));
    }
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
