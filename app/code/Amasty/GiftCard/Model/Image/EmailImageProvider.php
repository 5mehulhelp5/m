<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\Image;

use Amasty\GiftCard\Api\Data\ImageInterface;
use Amasty\GiftCard\Utils\FileUpload;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

/**
 * @api
 */
class EmailImageProvider
{
    /**
     * @var FileUpload
     */
    private $fileUpload;

    /**
     * @var OutputBuilderFactory
     */
    private $outputBuilderFactory;

    /**
     * @var Mime
     */
    private $mime;

    /**
     * @var Filesystem|null
     */
    private $filesystem;

    public function __construct(
        FileUpload $fileUpload,
        ?File $file, //@deprecated
        OutputBuilderFactory $outputBuilderFactory,
        Mime $mime,
        ?Filesystem $filesystem = null
    ) {
        $this->fileUpload = $fileUpload;
        $this->outputBuilderFactory = $outputBuilderFactory;
        $this->mime = $mime;
        $this->filesystem = $filesystem ?: ObjectManager::getInstance()->get(Filesystem::class);
    }

    public function get(ImageInterface $image, string $code): string
    {
        $imagePath = $this->fileUpload->getImagePath($image);
        $media = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        if (!$media->isExist($imagePath)) {
            return '';
        }

        $fileContent = $media->getDriver()->fileGetContents($imagePath);
        $result = '<div style="overflow:hidden;position:relative;height:' . $image->getHeight()
            . 'px;width:' . $image->getWidth() . 'px;"><img style="height:100%;width:100%;" src="data:'
            . $this->mime->getMimeType($imagePath)
            . ';base64,' . base64_encode($fileContent) . '">';

        if (!$image->isUserUpload() && $image->getImageElements()) {
            $result .= $this->outputBuilderFactory->create(OutputBuilderFactory::HTML_BUILDER, ['code' => $code])
                ->build($image->getImageElements());
        }
        $result .= '</div>';

        return $result;
    }
}
