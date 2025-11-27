<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sevit\PrikolBot\Commands;
use Sevit\PrikolBot\Modules\Core\Application;
use Sevit\PrikolBot\Modules\Core\Configuration;
use Sevit\PrikolBot\Modules\Core\Enums\ChatType;
use Sevit\PrikolBot\Modules\Core\Routing\RouteList;
use Sevit\PrikolBot\Middlewares;

$routeList = new RouteList();
$routeList->addCommand('start', Commands\StartCommand::class);
$routeList->addTextCondition('В прикол', Commands\UploadCommand::class)
    ->addMiddleware(Middlewares\CheckCanUpload::class)
    ->addAvailableChatType(ChatType::Group);
$routeList->addStandardHandlerForChatTypes([ChatType::Private], Commands\UploadCommand::class);

$app = new Application(
    new Configuration(
        rootPath: dirname(__DIR__)
    ),
    $routeList,
);
$app->run();