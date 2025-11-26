<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use Sevit\PrikolBot\Response;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;

final readonly class UploadCommand implements InvokableCommandInterface
{
    public function __construct(
        private BotApi $botApi,
    ) {
    }

    public function __invoke(Update $update): Response
    {
        $message = $update->getMessage();

        $hasPhoto = (bool)$message->getPhoto();
        if ($hasPhoto) {
            $photoSizes = $message->getPhoto();
            $maxPhotoSize = $photoSizes[count($photoSizes) - 1];
            $fileInfo = $this->botApi->getFile($maxPhotoSize->getFileId());
            $fileBinary = $this->botApi->downloadFile($maxPhotoSize->getFileId());

            $pathParts = explode('/', $fileInfo->getFilePath());
            $fileName = $pathParts[count($pathParts) - 1];
            $fileNameParts = explode( '.', $fileName);
            $fileExtension = $fileNameParts[count($fileNameParts) - 1];

            $tmpFilePath = '../storage/tmp/' . $fileInfo->getFileUniqueId() . '.' . $fileExtension;

            $file = fopen($tmpFilePath, 'wb');
            fwrite($file, $fileBinary);

            return new Response(
                'Отлично! Сохранил это в хранилище!',
            );
        }

        return new Response(
            'Похоже, что вы не указали ничего, что я мог бы сохранить.',
        );
    }
}