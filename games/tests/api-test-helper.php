<?php
// Test helper: simulates HTTP requests to api.php via CLI
$method = $argv[1] ?? 'GET';
$body = $argv[2] ?? '';

$_SERVER['REQUEST_METHOD'] = $method;
$_SERVER['REQUEST_URI'] = '/games/api.php';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Write body to temp file
$tmpFile = tempnam(sys_get_temp_dir(), 'api_test');
file_put_contents($tmpFile, $body);

// Read api.php and replace php://input with our temp file
$apiFile = dirname(__DIR__) . '/api.php';
$code = file_get_contents($apiFile);

// Replace __DIR__ references to use the games directory
$gamesDir = dirname(__DIR__);
$code = str_replace("__DIR__ . '/queue'", "'$gamesDir/queue'", $code);
$code = str_replace(
    "file_get_contents('php://input')",
    "file_get_contents('$tmpFile')",
    $code
);

header_remove();
ob_start();
eval('?>' . $code);
$output = ob_get_clean();
unlink($tmpFile);

echo $output;
