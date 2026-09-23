<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Image\Utils;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Model\Image\Utils\AdditionalExtensionsChecker;
use Amasty\GiftCard\Model\Image\Utils\ImageGenerator\ImageGeneratorAdapterInterface;
use Amasty\GiftCard\Model\Pdf\PdfImageConverter;
use Amasty\GiftCardAccount\Model\GiftCardAccount\Repository;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Moved from Amasty\GiftCard\Model\Image\Utils\ImageHtmlToFileConverter
 */
class ImageHtmlToFileConverter
{
    public const AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH = 'amasty/amgcard/image/generated_images_cache';

    /**
     * @var AdditionalExtensionsChecker
     */
    private $extensionsChecker;

    /**
     * @var PdfImageConverter
     */
    private $pdfImageConverter;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $mediaDir;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var File
     */
    private $ioFile;

    /**
     * @var Repository
     */
    private $accountRepository;

    /**
     * @var ImageGeneratorAdapterInterface|null
     */
    private $imageGenerator;

    public function __construct(
        AdditionalExtensionsChecker $extensionsChecker,
        PdfImageConverter $pdfImageConverter,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        File $ioFile,
        Repository $accountRepository,
        ?ImageGeneratorAdapterInterface $imageGenerator = null
    ) {
        $this->extensionsChecker = $extensionsChecker;
        $this->pdfImageConverter = $pdfImageConverter;
        $this->mediaDir = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->accountRepository = $accountRepository;
        $this->imageGenerator = $imageGenerator
            ?: ObjectManager::getInstance()->get(ImageGeneratorAdapterInterface::class);
    }

    /**
     * Trying to convert image from HTML string to file for img src attribute
     * for better compatibility with different email clients.
     * Return original HTML in case of error.
     *
     * @param ImageInterface $image
     * @param string $code
     * @param string $html
     * @return string
     */
    public function convert(ImageInterface $image, string $code, string $html): string
    {
        try {
            if ($imageName = $this->createImageFile($image, $code, $html)) {
                $imagePath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH
                    . DIRECTORY_SEPARATOR . $imageName;

                return '<img src="' . $imagePath . '" />';
            }
        } catch (\Exception $e) {//phpcs:ignore Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            //no need to process exception
        }

        return $html;
    }

    public function createImageFile(ImageInterface $image, string $code, string $html): string
    {
        if (!$this->extensionsChecker->isImagickEnabled()) {
            return '';
        }

        $extension = $this->ioFile->getPathInfo($image->getImagePath())['extension'] ?? '';
        if (!$extension) {
            return '';
        }
        $filename = $code . '.' . $extension;
        $mediaPath = self::AMGIFTCARD_RENDERED_IMAGE_MEDIA_PATH . DIRECTORY_SEPARATOR . $filename;
        $account = $this->accountRepository->getByCode($code);
        if (!$this->mediaDir->isExist($mediaPath) || !$account->isImageGenerated()) {
            if (!($pdfString = $this->pdfImageConverter->convert($html, $code))) {
                return '';
            }
            //regenerate image because of possible changes
            $this->mediaDir->delete($mediaPath);
            $this->imageGenerator->initImage($pdfString, $image->getWidth(), $image->getHeight());
            $this->imageGenerator->writeImage($mediaPath);
            $account->setIsImageGenerated(true);
            $this->accountRepository->save($account);
        }

        return $filename;
    }
}
