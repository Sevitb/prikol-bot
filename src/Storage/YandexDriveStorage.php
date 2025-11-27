<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Storage;

use Sevit\PrikolBot\Storage\Entities\File;

final readonly class YandexDriveStorage implements StorageInterface
{
    public function __construct(
        private Client $client,
    ) {
    }

    public function upload(File $file): bool
    {

    }
}