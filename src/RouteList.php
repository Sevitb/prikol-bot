<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Closure;
use TelegramBot\Api\Types\Message;

final class RouteList
{
    /**
     * @var array<array{condition: Closure, route: Route}>
     */
    private array $conditions = [];

    /**
     * @var array<string, Route>
     */
    private array $textConditions = [];

    public static function create(): self
    {
        return new self();
    }

    public function addCommand(string $name, string|Closure $handler): Route
    {
        return $this->addTextCondition('/' . $name, $handler);
    }

    public function addTextCondition(string $textCondition, string|Closure $handler): Route
    {
        $route = new Route(
            $handler,
        );
        $this->textConditions[mb_strtolower($textCondition)] = $route;

        return $route;
    }

    public function addCondition(callable $condition, string|Closure $handler): Route
    {
        $route = new Route(
            $handler,
        );
        $this->conditions[] = [
            'condition' => $condition,
            'route' => $route,
        ];

        return $route;
    }

    public function getByCondition(Message $message): ?Route
    {
        foreach ($this->conditions as $condition) {
            if ($condition['condition']($message)) {
                return $condition['rote'];
            }
        }

        return null;
    }

    public function getByTextCondition(string $textCondition): ?Route
    {
        return $this->textConditions[mb_strtolower($textCondition)] ?? null;
    }
}