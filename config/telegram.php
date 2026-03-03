<?php

return [
    'bot_token' => env('TELEGRAM_BOT_TOKEN', ''),
    'bot_username' => env('TELEGRAM_BOT_USERNAME', env('TELEGRAM_BOT_NAME', '')),
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET', ''),
];
