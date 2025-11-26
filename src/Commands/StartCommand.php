<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use Sevit\PrikolBot\Response;
use TelegramBot\Api\Types\Update;

final readonly class StartCommand implements InvokableCommandInterface
{
    public function __invoke(Update $update): Response
    {
        $message = $update->getMessage();
        return new Response(
            'Здарова, @' . $message->getFrom()->getUsername() . '! Есть чем поделиться?',
        );
    }
}