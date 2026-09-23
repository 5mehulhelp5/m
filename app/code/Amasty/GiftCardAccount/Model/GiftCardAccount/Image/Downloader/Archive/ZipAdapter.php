<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Archive;

use Amasty\GiftCard\Model\Image\Utils\RemoteStorageProcessor;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils\DownloadableFile;
use Magento\Framework\App\ObjectManager;

class ZipAdapter implements ArchiveAdapterInterface
{
    private const EXTENSION = 'zip';

    /**
     * @var RemoteStorageProcessor
     */
    private $remoteStorageProcessor;

    public function __construct(
        ?RemoteStorageProcessor $remoteStorageProcessor = null // TODO move to not optional
    ) {
        if (!class_exists('ZipArchive')) {
            throw new \RuntimeException(
                'PHP extension Zip is not installed on your server. Please, '
                . 'contact your system administrator or your hosting provider'
                . ' to install this extension on your server.'
            );
        }
        $this->remoteStorageProcessor = $remoteStorageProcessor
            ?: ObjectManager::getInstance()->get(RemoteStorageProcessor::class);
    }

    public function pack(string $destination, array $images): void
    {
        if (!$this->remoteStorageProcessor->isRemoteStorageEnabled()) {
            $this->processLocalPack($destination, $images);
        } else {
            $this->processRemoteStoragePack($destination, $images);
        }
    }

    public function getExtension(): string
    {
        return self::EXTENSION;
    }

    private function processLocalPack(string $destination, array $images): void
    {
        $zip = new \ZipArchive();
        $zip->open($destination, \ZipArchive::CREATE);

        foreach ($images as $downloadableImage) {
            $zip->addFile($downloadableImage->getPath(), $downloadableImage->getFileName());
        }

        $zip->close();
    }

    private function processRemoteStoragePack(string $destination, array $images): void
    {
        $zip = new \ZipArchive();
        $destination = $this->remoteStorageProcessor->convertPath($destination);
        $zip->open($destination, \ZipArchive::CREATE);

        foreach ($images as $downloadableImage) {
            $localPath = $this->copyImageLocal($downloadableImage);
            $zip->addFile($localPath, $downloadableImage->getFileName());
        }

        $zip->close();
        $this->remoteStorageProcessor->copyToRemote($destination);
    }

    private function copyImageLocal(DownloadableFile $downloadableImage): string
    {
        $imgPath = $downloadableImage->getPath();
        $this->remoteStorageProcessor->copyFromRemote($imgPath);

        return $this->remoteStorageProcessor->convertPath($imgPath);
    }
}
