<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Closure;
use Exception;
use Psr\Log\LoggerInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;
use Throwable;

final readonly class Bot
{
    public function __construct(
        private BotApi $botApi,
        private RouteList $routeList,
        private LoggerInterface $logger,
    ) {
    }

    public function runLongPolling(): void
    {
        $offset = 0;
        while (true) {
            $updates = $this->botApi->getUpdates($offset);

            $this->handleUpdates($updates);

            if ($lastUpdate = end($updates)) {
                $offset = $lastUpdate->getUpdateId() + 1;
            }
            sleep(10);
        }
    }

    public function runWebhook(): void
    {
        if ($data = $this->botApi->jsonValidate((string) $this->getRawBody(), true)) {
            /** @var array $data */
            $this->handleUpdate(Update::fromResponse($data));
        }
    }

    private function getRawBody()
    {
        return file_get_contents('php://input');
    }

    private function handleUpdates(array $updates): void
    {
        foreach ($updates as $update) {
            try {
                $this->handleUpdate($update);
            } catch (Throwable $exc) {
                $this->logger->error('Не удалось обработать обновление: ' . $exc->getMessage());
                $this->botApi->sendMessage($update->getMessage()->getChat()->getId(), 'Не удалось обработать сообщение: ' . $update->getMessage()->getText());
            }
        }
    }

    private function handleUpdate(Update $update): void
    {
        $route = $this->resolve($update);

        $response = $this->process($route, $update);

        if ($response->hasTextData()) {
            $this->botApi->sendMessage($update->getMessage()->getChat()->getId(), $response->getTextData());
        }
    }

    private function resolve(Update $update): ?Route
    {
        $message = $update->getMessage();
        $text = $message->getText() ?? $message->getCaption();
        if ($route = $this->routeList->getByTextCondition($text)) {
            return $route;
        }

        return $this->routeList->getByCondition($message);
    }

    private function process(Route $route, Update $update): Response
    {
        $handler = $route->getHandler();
        if (is_string($handler) && class_exists($handler)) {
            $handler = new $handler($this->botApi);
        } elseif (!$handler instanceof Closure) {
            throw new Exception('Неизвестный обработчик: ' . $handler);
        }

        $pipeline = $handler;
        foreach (array_reverse($route->getMiddlewares()) as $middleware) {
            $pipeline = function(Update $update) use ($middleware, $pipeline) {
                return $middleware->handle($update, $pipeline);
            };
        }

        return $pipeline($update);
    }
}