<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Enums;

enum ChatType: string
{
    case Private = 'private';
    case Group = 'group';
    case Supergroup = 'supergroup';
    case All = 'all';

    public function getId(): int
    {
        return match ($this) {
            self::Private => 1,
            self::Group => 2,
            self::Supergroup => 4,
            self::All => self::Private->getId() | self::Group->getId() | self::Supergroup->getId(),
        };
    }
}