<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Middlewares;

use Sevit\PrikolBot\Modules\Core\Response;
use TelegramBot\Api\Types\Update;

interface MiddlewareInterface
{
    public function handle(Update $update, callable $next): Response;
}