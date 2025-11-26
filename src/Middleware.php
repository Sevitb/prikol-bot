<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use TelegramBot\Api\Types\Update;

class Middleware
{
    public function handle(Update $update, callable $next)
    {

        return $next($update);
    }
}