<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Utils;

use Sevit\PrikolBot\Modules\Core\Enums\ChatType;

final readonly class ChatTypeUtil
{
    /**
     * @param ChatType[] $chatTypes
     * @return int
     */
    public static function getChatTypesCode(array $chatTypes): int
    {
        return array_reduce($chatTypes, static fn(int $carry, ChatType $chatType) => $carry | $chatType->getId(), 0);
    }
}