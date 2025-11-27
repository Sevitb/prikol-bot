<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Routing;

use Closure;
use Sevit\PrikolBot\Modules\Core\Enums\ChatType;
use Sevit\PrikolBot\Modules\Core\Utils\ChatTypeUtil;
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

    /**
     * @var array<string, Route>
     */
    private array $standardChatTypeRoutes = [];

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
        $route = new Route($handler);
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

    /**
     * @param array $chatTypes
     * @param class-string|Closure $handler
     * @return Route
     */
    public function addStandardHandlerForChatTypes(array $chatTypes, string|Closure $handler): Route
    {
        $route = new Route($handler);
        $code = ChatTypeUtil::getChatTypesCode($chatTypes);
        $this->standardChatTypeRoutes[$code] = $route;

        return $route;
    }

    public function getStandardForChatType(ChatType $chatType): ?Route
    {
        if (isset($this->standardChatTypeRoutes[$chatType->getId()])) {
            return $this->standardChatTypeRoutes[$chatType->getId()];
        }

        foreach ($this->standardChatTypeRoutes as $code => $route) {
            if ($code & $chatType->getId()) {
                return $route;
            }
        }

        return null;
    }
}