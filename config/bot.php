<?php

declare(strict_types=1);

return [
    'telegram_bot_token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? null,
    'storage_path' => 'storage',
    'network_operating_mode' => 'longPolling' // webhook/longPolling
];