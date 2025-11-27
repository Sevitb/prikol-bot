<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Routing;

use Closure;
use Sevit\PrikolBot\Modules\Core\Enums\ChatType;
use Sevit\PrikolBot\Modules\Core\Middlewares\MiddlewareInterface;

final class Route
{
    /**
     * @var class-string<MiddlewareInterface>
     */
    private array $middlewares = [];

    /**
     * @param class-string|Closure $handler
     * @param ChatType[] $availableChatTypes
     */
    public function __construct(
        private string|Closure $handler,
        private array $availableChatTypes = [ChatType::Private],
    ) {
    }

    public function getHandler(): string|Closure
    {
        return $this->handler;
    }

    public function addMiddleware(string $middlewareClassString): self
    {
        $this->middlewares[] = $middlewareClassString;
        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * @param ChatType[] $availableChatTypes
     * @return self
     */
    public function setAvailableChatTypes(array $availableChatTypes): self
    {
        $this->availableChatTypes = $availableChatTypes;
        return $this;
    }

    public function addAvailableChatType(ChatType $chatType): self
    {
        $this->availableChatTypes[] = $chatType;
        return $this;
    }

    public function getAvailableChatTypes(): array
    {
        return $this->availableChatTypes;
    }
}