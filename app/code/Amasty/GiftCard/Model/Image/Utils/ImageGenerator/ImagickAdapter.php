<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image\Utils\ImageGenerator;

use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Amasty\GiftCard\Model\Image\Utils\RemoteStorageProcessor;
use Amasty\GiftCardAccount\Model\Image\Utils\ImageHtmlToFileConverter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverPool;

class ImagickAdapter implements ImageGeneratorAdapterInterface
{
    private const DEFAULT_POS = 0;

    /**
     * @var RemoteStorageProcessor
     */
    private $remoteStorageProcessor;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $localMedia;

    /**
     * @var \Imagick|null
     */
    private $image;

    public function __construct(
        AdditionalExtensionsChecker $additionalExtensionsChecker,
        RemoteStorageProcessor $remoteStorageProcessor,
        Filesystem $filesystem
    ) {
        if (!$additionalExtensionsChecker->isImagickEnabled()) {
            throw new \RuntimeException(
                '\'ext-imagick\' extension not found. '
                . 'Please install it to use image converting.'
            );
        }
        $this->remoteStorageProcessor = $remoteStorageProcessor;
        $this->localMedia = $filesystem->getDirectoryWrite(DirectoryList::MEDIA, DriverPool::FILE);
    }

    public function initImage(string $imageBlob, int $width, int $height): void
    {
        $this->image = new \Imagick();
        $this->image->setSize($width, $height);
        $this->image->readImageBlob($imageBlob);
        $this->image->cropImage($width, $height, self::DEFAULT_POS, self::DEFAULT_POS);
    }

    public function writeImage(string $filepath): void
    {
        if (!$this->image) {
            return;
        }
        if (!$this->localMedia->isExist(ImageHtmlToFileConverter::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH)) {
            $this->localMedia->create(ImageHtmlToFileConverter::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH);
        }

        $this->image->writeImage($this->localMedia->getAbsolutePath($filepath));
        if ($this->remoteStorageProcessor->isRemoteStorageEnabled()) {
            $this->remoteStorageProcessor->copyToRemote($filepath);
            $this->localMedia->delete($filepath);
        }
    }
}
