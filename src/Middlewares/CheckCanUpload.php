<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Middlewares;

use Sevit\PrikolBot\Modules\Core\Entities\Response;
use Sevit\PrikolBot\Modules\Core\Exceptions\BotException;
use Sevit\PrikolBot\Modules\Core\Middlewares\MiddlewareInterface;
use TelegramBot\Api\Types\Update;

final readonly class CheckCanUpload implements MiddlewareInterface
{
    public function handle(Update $update, callable $next): Response
    {
        $updateAuthorId = $update->getMessage()->getFrom()->getId();

        $canUploadIds = [
            773514874
        ];

        if (!in_array($updateAuthorId, $canUploadIds)) {
            throw new BotException('У тебя нет прав на загрузку файлов((');
        }

        return $next($update);
    }
}