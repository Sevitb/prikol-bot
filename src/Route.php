<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Closure;

final class Route
{
    private array $middlewares = [];

    public function __construct(
        private string|Closure $handler,
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
}