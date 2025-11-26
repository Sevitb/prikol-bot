<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Closure;

final class RouteCondition
{
    private array $conditions = [];

    public function __construct(
        private Closure $handler,
    ) {
    }

    public function addCondition(Closure $condition): self
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function setMiddlewares(): self
    {
        return $this;
    }

    public function addMiddleware(): self
    {
        return $this;
    }
}