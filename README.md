## README.txt

# Make Me Laugh Bot

Welcome to the Make Me Laugh Bot! This is a simple Telegram bot that responds to user commands with random jokes. The bot is written in PHP and uses the Telegram Bot API along with a joke API to fetch jokes.

### Project Structure

```
project_root/
│
├── bot.php          # Main bot logic
├── config.php       # Configuration file containing the bot token
├── composer.json    # Dependencies for the project
└── README.txt       # This readme file
```

### Prerequisites

- PHP installed on your local machine (PHP 7.4+ recommended)
- Composer installed for managing dependencies
- A Telegram bot token (created using BotFather)
- Ngrok for local HTTPS tunneling (optional, but recommended for learning purposes)

### Setup Instructions

1. **Clone the Repository**:
   ```sh
   git clone <repository_url>
   cd project_root
   ```

2. **Install Dependencies**:
   ```sh
   composer install
   ```

3. **Configure Your Bot Token**:
   - Open the `config.php` file and replace `YOUR_BOT_TOKEN` with your actual Telegram bot token.
   - `config.php`:
     ```php
     <?php
     return [
         'BOT_TOKEN' => 'YOUR_BOT_TOKEN'
     ];
     ?>
     ```

4. **Run the Bot Locally**:
   - Start a PHP server:
     ```sh
     php -S localhost:8000
     ```
   - Use Ngrok to create a secure tunnel to your local server:
     ```sh
     ngrok http 8000
     ```
   - Copy the HTTPS URL provided by Ngrok.

5. **Set the Webhook**:
   - Replace `<YourBotToken>` with your bot token and `<NgrokURL>` with the HTTPS URL provided by Ngrok in the following URL:
     ```sh
     https://api.telegram.org/bot<YourBotToken>/setWebhook?url=<NgrokURL>/bot.php
     ```
   - Open this URL in your browser to set the webhook.

### Usage

- Start a conversation with your bot on Telegram.
- Send the `/start` command to see a welcome message.
- Send "joke" or "tell me a joke" to receive a random joke.

### Bot Logic

- **/start Command**: Sends a welcome message.
- **joke or tell me a joke**: Fetches a random joke from the `icanhazdadjoke` API and sends it to the user.
- **Default Response**: If the message doesn't match any command, sends a help message.

### Example Code

#### `config.php`
```php
<?php
return [
    'BOT_TOKEN' => 'YOUR_BOT_TOKEN'
];
?>
```

#### `bot.php`
```php
<?php
// Load configuration
$config = require 'config.php';

// Fetch the bot token from the configuration
$token = $config['BOT_TOKEN'];

// Fetching the update sent by Telegram
$update = json_decode(file_get_contents("php://input"), TRUE);

// Extracting chat ID, username, and message text from the update
$chatId = $update["message"]["chat"]["id"] ?? null;
$userName = $update["message"]["chat"]["first_name"] ?? null;
$message = $update["message"]["text"] ?? null;

// Function to send message to the user
function sendMessage($chatId, $message, $token) {
    $url = 'https://api.telegram.org/bot'.$token.'/sendMessage?chat_id='.$chatId.'&text='.urlencode($message);
    file_get_contents($url);
}

// Handling the /start command
if ($message !== null && stripos($message, "/start") === 0) {
    $welcomeMessage = "Hello, " . $userName . "! Welcome to the Make Me Laugh bot. Type 'joke' or 'tell me a joke' to get a random joke!";
    sendMessage($chatId, $welcomeMessage, $token);
} elseif ($message !== null && stripos($message, "joke") !== false) {
    // Joke API endpoint
    $url = 'https://icanhazdadjoke.com/';
    
    // Initializing cURL session
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'MakeMeLaugh/1.0 (http://www.mysite.com/)');
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
```

### Important Note

Free hosting services often have limitations that might prevent your bot from staying live continuously. They may block frequent requests or limit the execution time of scripts, causing the bot to be unreliable. 

For learning purposes, it's better to use a local environment with Ngrok for a more stable experience. This way, you can control and monitor the bot’s behavior closely without restrictions imposed by free hosting providers.

### Conclusion

By following this guide, you should be able to set up and run your Telegram bot locally. For a more stable deployment, consider using a paid hosting service or cloud platform that offers better reliability and support for long-running processes.

Happy coding!
