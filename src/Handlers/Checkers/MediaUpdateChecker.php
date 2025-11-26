<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Handlers\Checkers;

final readonly class MediaUpdateChecker
{
    public function __invoke(): bool
    {
        return true;
    }
}