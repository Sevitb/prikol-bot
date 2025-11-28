<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core;

use Analog;
use Analog\Handler\File;
use Analog\Handler\Multi;
use Analog\Logger;
use Closure;
use Psr\Log\LoggerInterface;
use Sevit\PrikolBot\Modules\Core\Entities\Response;
use Sevit\PrikolBot\Modules\Core\Enums\ChatType;
use Sevit\PrikolBot\Modules\Core\Exceptions\BotException;
use Sevit\PrikolBot\Modules\Core\Routing\Route;
use Sevit\PrikolBot\Modules\Core\Routing\RouteList;
use Sevit\PrikolBot\Modules\Core\Utils\ChatTypeUtil;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use TelegramBot\Api\Types\Animation;
use TelegramBot\Api\Types\Update;
use Throwable;

final class Application
{
    public function __construct(
        private readonly Configuration $config,
        private readonly RouteList $routeList,
        private ?BotApi $botApi = null,
        private ?LoggerInterface $logger = null,
    ) {
        $this->botApi ??= $this->initBotApi();
        $this->logger ??= $this->initLogger();
    }

    public function run(): void
    {
        match ($this->config->getBotSetting('network_operating_mode')) {
            'webhook' => $this->runWebhook(),
            'longPolling' => $this->runLongPolling(),
            default => throw new Exception('Установлен несуществующий тип функционирования бота'),
        };
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
            } catch (BotException $exc) {
                $this->botApi->sendMessage($update->getMessage()->getChat()->getId(), $exc->getMessage());
            } catch (Throwable $exc) {
                $this->logger->error('Не удалось обработать обновление: ' . $exc->getMessage());
                $this->botApi->sendMessage($update->getMessage()->getChat()->getId(), 'Не удалось обработать сообщение.');
            }
        }
    }

    private function handleUpdate(Update $update): void
    {
        // Пока обрабатываем только исходные сообщение
        // Если приходит update с отредактированным сообщением, его скипаем
        if($update->getMessage() === null || !$route = $this->resolve($update)) {
            return;
        }

        $chatId = $update->getMessage()->getChat()->getId();
        $response = $this->process($route, $update);

        foreach ($response->getMessages() as $message) {
            if ($message->getAnimation()) {
                $this->botApi->sendAnimation(
                    chatId: $chatId,
                    animation: $message->getAnimation(),
                    caption: $message->getText(),
                    parseMode: $message->getParseMode()?->value,
                );
                continue;
            }
            if ($message->getText()) {
                $this->botApi->sendMessage(
                    chatId: $chatId,
                    text: $message->getText(),
                    parseMode: $message->getParseMode()?->value,
                );
            }
        }
    }

    private function resolve(Update $update): ?Route
    {
        $message = $update->getMessage();
        $chat = $message->getChat();
        $text = $message->getText() ?? $message->getCaption();

        $route = isset($text) ? $this->routeList->getByTextCondition($text) : null;
        if (!isset($route)) {
            $route = $this->routeList->getByCondition($message);
        }

        $available = ChatTypeUtil::getChatTypesCode($route?->getAvailableChatTypes() ?? []);

        $chatType = ChatType::from($chat->getType());
        if ($chatType->getId() & $available) {
            return $route;
        }

        if ($route = $this->routeList->getStandardForChatType($chatType)) {
            return $route;
        }

        if (!$this->routeList->isUndefinedRoutesIgnoredForChatType($chatType)) {
            return null;
        }

        throw new BotException('Не понимаю о чем ты ¯\_(ツ)_/¯');
    }

    private function process(Route $route, Update $update): Response
    {
        $handler = $route->getHandler();
        if (is_string($handler) && class_exists($handler)) {
            $handler = new $handler($this->botApi, $this->config);
        } elseif (!$handler instanceof Closure) {
            throw new Exception('Неизвестный обработчик: ' . $handler);
        }

        $pipeline = $handler;
        foreach (array_reverse($route->getMiddlewares()) as $middlewareClassString) {
            $pipeline = function(Update $update) use ($middlewareClassString, $pipeline) {
                return (new $middlewareClassString())->handle($update, $pipeline);
            };
        }

        return $pipeline($update);
    }

    private function initBotApi(): BotApi
    {
        return new BotApi($this->config->getBotSetting('telegram_bot_token'));
    }

    private function initLogger(): LoggerInterface
    {
        $logger = new Logger();
        $baseLogPath = $this->config->getRootPath() . '/' . $this->config->getLogSetting('base_path');
        $logger->handler(Multi::init(
            [
                Analog::ERROR => File::init($baseLogPath . '/' . $this->config->getLogSetting('error_file')),
                Analog::INFO => File::init($baseLogPath . '/' . $this->config->getLogSetting('access_file')),
            ]
        ));

        return $logger;
    }
}