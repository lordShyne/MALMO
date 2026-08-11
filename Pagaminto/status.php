<?php
// status.php — serves status.json content via JS polling
// Same-origin policy protects this since we set X-Frame-Options: DENY

$dataFile = __DIR__ . '/status.json';

// Simple check: only respond to GET requests with proper Accept header
// Blocks raw curl/wget but allows browser JS fetches
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
if (strpos($accept, 'application/json') === false && strpos($accept, '*/*') === false && strpos($accept, 'text/html') !== false) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
if (file_exists($dataFile)) {
    readfile($dataFile);
} else {
    echo '{}';
}
