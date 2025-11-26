<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Sevit\PrikolBot\Application;
use Sevit\PrikolBot\Commands;
use Sevit\PrikolBot\Configuration;
use Sevit\PrikolBot\RouteList;

$routeList = new RouteList();
$routeList->addCommand('start', Commands\StartCommand::class);
$routeList->addTextCondition('В прикол', Commands\UploadCommand::class);
$routeList->addTextCondition('Привет', static fn() => new \Sevit\PrikolBot\Response('Привет, привет. Есть че сохранить?'));

$app = new Application(
    new Configuration(),
    $routeList,
);
$app->run();