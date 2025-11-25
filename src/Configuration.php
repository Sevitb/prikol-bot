<?php

declare(strict_types=1);

namespace Sevit\PrikolBot;

use Dotenv\Dotenv;

final class Configuration
{
    private array $settings = [];

    public function __construct()
    {
        $this->loadEnv();
    }

    private function loadEnv(): void
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->safeLoad();
    }

    public function getBotSetting(string $setting): mixed
    {
        return $this->get('bot', $setting);
    }

    public function getLogSetting(string $setting): mixed
    {
        return $this->get('logging', $setting);
    }

    public function get(string $fileName, string $setting): mixed
    {
        $settings = $this->getSettingsFile($fileName);

        return $settings[$setting] ?? null;
    }

    private function getSettingsFile(string $fileName): ?array
    {
        if (!isset($settings[$fileName])) {
            $settings[$fileName] = require $this->getRootPath() . '/config/' . $fileName . '.php';
        }

        return $settings[$fileName] ?? null;
    }

    public function getRootPath(): string
    {
        return '../';
    }
}