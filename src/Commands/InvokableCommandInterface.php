<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use Sevit\PrikolBot\Response;
use TelegramBot\Api\Types\Update;

interface InvokableCommandInterface
{
    public function __invoke(Update $update): Response;
}