<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Analog\Handler\File;
use Analog\Handler\Multi;
use Analog\Logger;
use Sevit\PrikolBot\Bot;
use Sevit\PrikolBot\Commands\StartCommand;
use Sevit\PrikolBot\Commands\PingCommand;
use Sevit\PrikolBot\Configuration;
use Sevit\PrikolBot\CommandList;

$config = new Configuration();
$logger = new Logger();
$baseLogPath = '..' . $config->getLogSetting('base_path');
$logger->handler(Multi::init(
    [
        Analog::ERROR => File::init($baseLogPath . '/' . $config->getLogSetting('error_file')),
        Analog::INFO => File::init($baseLogPath . '/' . $config->getLogSetting('access_file')),
    ]
));

$bot = new Bot(
    $config,
    (new CommandList())
        ->addCommand('ping', PingCommand::class)
        ->addCommand('start', StartCommand::class),
    $logger,
);

$bot->run();