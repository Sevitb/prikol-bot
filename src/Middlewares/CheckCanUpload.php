<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Middlewares;

use Sevit\PrikolBot\Modules\Core\Exceptions\BotException;
use Sevit\PrikolBot\Modules\Core\Middlewares\MiddlewareInterface;
use Sevit\PrikolBot\Modules\Core\Response;
use TelegramBot\Api\Types\Update;

final readonly class CheckCanUpload implements MiddlewareInterface
{
    public function handle(Update $update, callable $next): Response
    {
        $updateAuthorUsername = $update->getMessage()->getFrom()->getId();

        $update->getMessage()->getChat()->getType();

        $canUpload = [
            773514874
        ];

        if (!in_array($updateAuthorUsername, $canUpload)) {
            throw new BotException('Вы не можете загружать файлы((');
        }

        return $next($update);
    }
}