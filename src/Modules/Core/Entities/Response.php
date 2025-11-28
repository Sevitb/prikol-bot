<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Entities;

final class Response
{
    /**
     * @var ResponseMessage[]
     */
    private array $messages = [];

    public static function create(): self
    {
        return new self();
    }

    public function addMessage(ResponseMessage $message): self
    {
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @return ResponseMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}