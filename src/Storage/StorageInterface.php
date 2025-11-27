<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Storage;

use Sevit\PrikolBot\Storage\Entities\File;

interface StorageInterface
{
    public function upload(File $file): bool;
}