<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Entities;

use CURLFile;
use Sevit\PrikolBot\Modules\Core\Enums\MessageParseMode;

final readonly class ResponseMessage
{
    public function __construct(
        private ?string $text = null,
        private ?MessageParseMode $parseMode = null,
        private ?CURLFile $animation = null,
    ) {
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getParseMode(): ?MessageParseMode
    {
        return $this->parseMode;
    }

    public function getAnimation(): ?CURLFile
    {
        return $this->animation;
    }

    public function hasContent(): bool
    {
        return !empty($this->text) || !isset($this->animation);
    }
}