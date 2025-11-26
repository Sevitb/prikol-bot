<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Analog;
use Analog\Handler\File;
use Analog\Handler\Multi;
use Analog\Logger;
use Psr\Log\LoggerInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;

final readonly class Application
{
    public function __construct(
        private Configuration $config,
        private RouteList $routeList,
    ) {
    }

    public function run(): void
    {
        $bot = new Bot(
            $this->initBotApi(),
            $this->routeList,
            $this->initLogger(),
        );

        match ($this->config->getBotSetting('network_operating_mode')) {
            'webhook' => $bot->runWebhook(),
            'longPolling' => $bot->runLongPolling(),
            default => throw new Exception('Установлен несуществующий тип функционирования бота'),
        };
    }

    private function initBotApi(): BotApi
    {
        return new BotApi($this->config->getBotSetting('telegram_bot_token'));
    }

    private function initLogger(): LoggerInterface
    {
        $logger = new Logger();
        $baseLogPath = '..' . $this->config->getLogSetting('base_path');
        $logger->handler(Multi::init(
            [
                Analog::ERROR => File::init($baseLogPath . '/' . $this->config->getLogSetting('error_file')),
                Analog::INFO => File::init($baseLogPath . '/' . $this->config->getLogSetting('access_file')),
            ]
        ));

        return $logger;
    }
}