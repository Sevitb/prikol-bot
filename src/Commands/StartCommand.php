<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;

final readonly class StartCommand implements InvokableCommandInterface
{
    public function __invoke(Client $botClient, Message $message): void
    {
        $id = $message->getChat()->getId();
        $botClient->sendMessage($id, 'Здарова, @' . $message->getFrom()->getUsername() . '! Есть чем поделиться?');
    }
}