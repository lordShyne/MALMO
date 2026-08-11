<?php
// PHP built-in server router — blocks direct access to sensitive files

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$ext = pathinfo($path, PATHINFO_EXTENSION);

// Block direct access to .json files
if ($ext === 'json') {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

// Block direct access to .htaccess, guard, router
$blocked = ['.htaccess', 'guard.php', 'router.php', 'action.php'];
foreach ($blocked as $b) {
    if (strpos($path, $b) !== false) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }
}

// Serve normally
return false;
