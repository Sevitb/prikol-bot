<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Storage\Entities;

final readonly class File
{
    public function __construct(
        private string $localPath,
        private string $name,
        private string $extension,
        private int $size,
    ) {
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}