<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Handlers;

use Sevit\PrikolBot\Actions\DownloadMedia;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Client;
use TelegramBot\Api\Types\Update;

final readonly class MediaUpdateHandler
{
    public function __construct(
        private BotApi $botApi,
    ) {
    }

    public function __invoke(Client $botClient, Update $update): void
    {

        $message = $update->getMessage();
        $download = new DownloadMedia($message);

        $download->downloadImages();

        $chatId = $message->getChat()->getId();

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
            $hash = hash_file('sha256', $tmpFilePath);

            $file = fopen($tmpFilePath, 'wb');
            fwrite($file, $fileBinary);

        }

        $botClient->sendMessage($id, 'Спасибо! Добавлю это в хранилище!');
    }
}