<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Entities;

final readonly class StorageFile
{
    public function __construct(
        private string $path,
        private string $extension,
        private int $size,
    ) {
    }
}