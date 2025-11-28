<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use CURLFile;
use Sevit\PrikolBot\Modules\Core\Entities\Response;
use Sevit\PrikolBot\Modules\Core\Entities\ResponseMessage;
use Sevit\PrikolBot\Modules\Core\Enums\MessageParseMode;
use TelegramBot\Api\Types\Animation;
use TelegramBot\Api\Types\Update;

final readonly class StartCommand
{
    public function __invoke(Update $update): Response
    {
        $message = $update->getMessage();
        return Response::create()
            ->addMessage(new ResponseMessage(
                text: 'Приветики, @' . $message->getFrom()->getUsername() . '\! Это прикол\-бот\.'
                    . "\n\n"
                    . 'Сюда ты можешь присылать видео/фото/аудио материалы, чтобы они оказались в приколе\.'
                    . "\n\n"
                    . 'Чтобы получить права на загрузку обратись к блэку ||\(негру\)||\.'
                    . "\n\n"
                    . 'P\.S\. Голые члены присылать нельзя\!\!\! ||\(не голые можно\)||',
                parseMode: MessageParseMode::Markdown,
                animation: new CURLFile('https://media1.tenor.com/m/6LaMwlpZIh4AAAAd/potato-spin.gif'),
            ));
    }
}