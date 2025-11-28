<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Entities;

final readonly class File
{
    public function __construct(
        private string $name,
        private string $extension,
        private string $binaryData,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getBinaryData(): string
    {
        return $this->binaryData;
    }
}