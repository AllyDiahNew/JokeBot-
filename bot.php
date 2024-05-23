<?php
// Load configuration
$config = require 'config.php';

// Fetch the bot token from the configuration
$token = $config['BOT_TOKEN'];

// Fetching the update sent by Telegram
$update = json_decode(file_get_contents("php://input"), TRUE);

// Extracting chat ID, username, and message text from the update
$chatId = $update["message"]["chat"]["id"];
$userName = $update["message"]["chat"]["first_name"];
$message = $update["message"]["text"];

// Function to send message to the user
function sendMessage($chatId, $message, $token) {
    $url = 'https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$chatId.'&text='.urlencode($message);
    file_get_contents($url);
}

// Handling the /start command
if (stripos($message, "/start") === 0) {
    $welcomeMessage = "Hello, " . $userName . "! Welcome to the Make Me Laugh. Type 'joke' or 'tell me a joke' to get a random joke!";
    sendMessage($chatId, $welcomeMessage, $token);
} elseif (stripos($message, "joke") !== false) {
    // Joke API endpoint
    $url = 'https://icanhazdadjoke.com/';
    
    // Initializing cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
   // curl_setopt($ch, CURLOPT_USERAGENT, 'MakeMeLaugh/1.0 (http://www.mysite.com/)');
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json"));
    
    // Executing the cURL request
    $result = curl_exec($ch);
    curl_close($ch);
    
    // Decoding the JSON response to fetch the joke
    $joke = json_decode($result, TRUE)["joke"];
    
    // Sending the joke to the user
    sendMessage($chatId, $joke . " 😂", $token);
} else {
    // Default message if the user does not ask for a joke
    $help = "Hi ".$userName."! I have infinite jokes with me 😎 Type \"Tell me a joke\" or just type \"Joke\", and I'll send you a random joke 😂";
    sendMessage($chatId, $help, $token);
}
?>
