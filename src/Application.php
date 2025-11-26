<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Psr\Log\LoggerInterface;
use Sevit\PrikolBot\Handlers\Checkers\MediaUpdateChecker;
use Sevit\PrikolBot\Handlers\MediaUpdateHandler;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Exception;

final readonly class Bot
{
    public function __construct(
        private Configuration $config,
        private CommandList $commandList,
        private LoggerInterface $logger,
    ) {
    }

    public function run(): void
    {
        match ($this->config->getBotSetting('network_operating_mode')) {
            'webhook' => $this->startWebhook(),
            'longPolling' => $this->startLongPolling(),
            default => throw new Exception('Установлен несуществующий тип функционирования бота'),
        };
    }

    private function startLongPolling(): void
    {
        $botApi = $this->initBotApi();
        $botClient = $this->initBotClient();

        $offset = 0;
        while (true) {
            $updates = $botApi->getUpdates($offset);

            $botClient->handle($updates);

            $lastUpdate = end($updates);
            if ($lastUpdate) {
                $offset = $lastUpdate->getUpdateId() + 1;
            }
            sleep(10);
        }
    }

    private function startWebhook(): void
    {
        try {
            $botClient = $this->initBotClient();
            $botClient->run();
        } catch (Exception $exc) {
            $this->logger->error($exc->getMessage(), [
                'exception' => $exc,
            ]);
        }
    }

    private function initBotClient(): Client
    {
        $bot = new Client($this->config->getBotSetting('telegram_bot_token'));

        foreach ($this->commandList as $name => $callable) {
            $class = new $callable();
            $bot->command($name, static fn () => $class($bot, ...func_get_args()));
        }

        $bot->on(static fn () => (new MediaUpdateHandler())($bot, ...func_get_args()), static fn () => (new MediaUpdateChecker())(...func_get_args()));

        return $bot;
    }

    private function initBotApi(): BotApi
    {
        return new BotApi($this->config->getBotSetting('telegram_bot_token'));
    }
}