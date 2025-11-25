<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;

interface InvokableCommandInterface
{
    public function __invoke(Client $botClient, Message $message): void;
}