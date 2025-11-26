<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Actions;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Message;

final readonly class DownloadMedia
{
    public function __construct(
        private BotApi $botApi,
        private Message $message,
    ) {
    }

    public function downloadImages(): bool
    {

    }

    public function __invoke(): bool
    {

    }
}