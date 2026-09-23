<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils;

use Amasty\GiftCard\Api\ImageRepositoryInterface;
use Amasty\GiftCard\Model\Image\EmailImageProvider;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\Image\Utils\ImageHtmlToFileConverter;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

class AccountImageProcessor
{
    /**
     * @var ImageRepositoryInterface
     */
    private $imageRepository;

    /**
     * @var EmailImageProvider
     */
    private $emailImageProvider;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var DownloadableFileFactory
     */
    private $downloadableFileFactory;

    /**
     * @var Filesystem\Directory\ReadInterface
     */
    private $mediaReader;

    public function __construct(
        ImageRepositoryInterface $imageRepository,
        EmailImageProvider $emailImageProvider,
        File $ioFile,
        Filesystem $filesystem,
        DownloadableFileFactory $downloadableFileFactory
    ) {
        $this->imageRepository = $imageRepository;
        $this->emailImageProvider = $emailImageProvider;
        $this->ioFile = $ioFile;
        $this->downloadableFileFactory = $downloadableFileFactory;
        $this->mediaReader = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    public function prepare(GiftCardAccountInterface $account): ?DownloadableFile
    {
        $image = $this->imageRepository->getById($account->getImageId());
        $extension = $this->ioFile->getPathInfo($image->getImagePath())['extension'] ?? '';
        if (!$extension || !$account->getCodeModel()) {
            return null;
        }

        $code = $account->getCodeModel()->getCode();
        $filename = $code . '.' . $extension;
        $expectedMediaPath = ImageHtmlToFileConverter::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH
            . DIRECTORY_SEPARATOR . $filename;

        if (empty($this->emailImageProvider->get($image, $code))
            || !$this->mediaReader->isExist($expectedMediaPath)
        ) {
            return null;
        }

        $downloadableFile = $this->downloadableFileFactory->create();
        $downloadableFile->setPath($this->mediaReader->getAbsolutePath($expectedMediaPath));
        $downloadableFile->setFileName($filename);

        return $downloadableFile;
    }
}
