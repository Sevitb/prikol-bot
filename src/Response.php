<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

final readonly class Response
{
    public function __construct(
        private ?string $text,
    ) {
    }

    public function hasTextData(): bool
    {
        return !empty($this->text);
    }

    public function getTextData(): ?string
    {
        return $this->text;
    }
}