<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Services;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Message;

final readonly class FileService
{
    public function __construct(
        private BotApi $botApi,
    ) {
    }

    public function downloadImages(Message $message): bool
    {
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

            return true;
        }

        return false;
    }
}