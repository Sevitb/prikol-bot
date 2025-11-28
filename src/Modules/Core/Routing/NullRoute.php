<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Modules\Core\Routing;

use Closure;
use Sevit\PrikolBot\Modules\Core\Enums\ChatType;

final class NullRoute extends Route
{
    public function __construct()
    {
        parent::__construct($handler, $availableChatTypes);
    }
}