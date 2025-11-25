<?php

declare(strict_types=1);

return [
    'telegram_bot_token' => $_ENV['TELEGRAM_BOT_TOKEN'] ?? null,
    'network_operating_mode' => 'longPolling' // webhook/longPolling
];