<?php

declare(strict_types=1);

namespace Sevit\PrikolBot\Commands;

use Exception;
use Sevit\PrikolBot\Modules\Core\Configuration;
use Sevit\PrikolBot\Modules\Core\Entities\File;
use Sevit\PrikolBot\Modules\Core\Entities\Response;
use Sevit\PrikolBot\Modules\Core\Entities\ResponseMessage;
use Sevit\PrikolBot\Modules\Core\Entities\StorageFile;
use Sevit\PrikolBot\Modules\Core\Exceptions\BotException;
use Sevit\PrikolBot\Modules\Core\Utils\FileUtil;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Document;
use TelegramBot\Api\Types\Update;
use TelegramBot\Api\Types\Video;
use TelegramBot\Api\Types\Voice;

final readonly class UploadCommand
{
    public function __construct(
        private BotApi $botApi,
        private Configuration $config,
    ) {
    }

    public function __invoke(Update $update): Response
    {
        $message = $update->getMessage();

        // Собрать StorageFile из Document
        // или
        // Собрать StorageFile из Photo
        // или
        // Собрать StorageFile из Video
        // или
        // Собрать StorageFile из Voice
        // Сохранить файл в системе
        // Залить сохраненный файл в хранилище
        // В случае успеха удалить файл из системы
        // В случае ошибки, не связанной с дублированием, оставить файл на месте, пометить как "Не отправленный" (переместить в папку send_later)
        // Проверить наличие mediaGroupId
        // Если есть:
        //  Проверить в redis/memcached данные по этой группе
        //

        $files = [];
        if ($document = $message->getDocument()) {
            $files[] = $this->getFileFromDocument($document);
        }
        if ($photoSizes = $message->getPhoto()) {
            $files[] = $this->getFileFromPhoto($photoSizes);
        }
        if ($video = $message->getVideo()) {
            $files[] = $this->getFileFromVideo($video);
        }
        if ($voice = $message->getVoice()) {
            $files[] = $this->getFileFromVoice($voice);
        }

        if (!$files) {
            return Response::create()
                ->addMessage(new ResponseMessage('Пришли файлы, которые хочешь сохранить.'));
        }

        $storageFiles = $this->saveFiles($files);



        return Response::create()
            ->addMessage(new ResponseMessage('Отлично! Положил это в хранилище!'));
    }

    /**
     * @param File[] $files
     * @return StorageFile[]
     */
    public function saveFiles(array $files): array
    {
        $storageFiles = [];
        foreach ($files as $file) {
            $tmpFilePath = $this->config->getRootPath() . '/' . $this->config->getBotSetting('storage_path') . '/tmp/' . $file->getName() . '.' . $file->getExtension();

            $fileSource = fopen($tmpFilePath, 'wb');
            fwrite($fileSource, $file->getBinaryData());
            fclose($fileSource);

            $storageFiles[] = new StorageFile(
                path: $tmpFilePath,
                extension: $file->getExtension(),
                size: strlen($file->getBinaryData()),
            );
        }

        return $storageFiles;
    }

    public function getFileFromDocument(Document $document): File
    {
        $extension = $this->getExtension($document->getMimeType());

        return new File(
            name: $document->getFileId(),
            extension: $extension,
            binaryData: $this->botApi->downloadFile($document->getFileId()),
        );
    }

    public function getFileFromPhoto(array $photoSizes): File
    {
        $maxPhotoSize = end($photoSizes);
        $fileInfo = $this->botApi->getFile($maxPhotoSize->getFileId());
        $pathParts = explode('/', $fileInfo->getFilePath());
        $fileName = end($pathParts);
        $fileNameParts = explode( '.', $fileName);
        $extension = end($fileNameParts);

        return new File(
            name: $maxPhotoSize->getFileId(),
            extension: $extension,
            binaryData: $this->botApi->downloadFile($maxPhotoSize->getFileId()),
        );
    }

    public function getFileFromVideo(Video $video): File
    {
        $extension = $this->getExtension($video->getMimeType());

        return new File(
            name: $video->getFileId(),
            extension: $extension,
            binaryData: $this->botApi->downloadFile($video->getFileId()),
        );
    }

    public function getFileFromVoice(Voice $voice): File
    {
        $extension = $this->getExtension($voice->getMimeType());

        return new File(
            name: $voice->getFileId(),
            extension: $extension,
            binaryData: $this->botApi->downloadFile($voice->getFileId()),
        );
    }

    public function getExtension(string $mime): string
    {
        $this->validateMime($mime);
        $extension = FileUtil::mime2ext($mime);
        if (!$extension) {
            throw new Exception('Неизвестное расширение файла.');
        }

        return $extension;
    }

    public function validateMime(string $mime): void
    {
        $availablePrefixes = [
            'image',
            'video',
            'audio',
        ];

        $prefix = explode('/', $mime);

        if (!in_array($prefix[0], $availablePrefixes)) {
            throw new BotException('Я не умею работать с файлами такого типа.');
        }
    }
}