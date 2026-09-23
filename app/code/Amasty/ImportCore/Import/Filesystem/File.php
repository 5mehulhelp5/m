<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Filesystem;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;

class File
{
    /**
     * @var WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var WriteInterface
     */
    private $rootDirectory;

    public function __construct(
        Filesystem $filesystem
    ) {
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->rootDirectory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
    }

    public function save($fileName, $importDirectory, $destinationPath): bool
    {
        $fullFilePath = $importDirectory . DIRECTORY_SEPARATOR . $fileName;
        $fileAbsolutePath = $this->rootDirectory->getAbsolutePath($fullFilePath);
        $fullFileDestinationPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
        $destinationAbsolutePath = $this->mediaDirectory->getAbsolutePath($fullFileDestinationPath);
        try {
            if (!$this->mediaDirectory->isDirectory($destinationPath)) {
                $this->mediaDirectory->create($destinationPath);
            }

            return $this->rootDirectory->copyFile(
                $fileAbsolutePath,
                $destinationAbsolutePath
            );
        } catch (FileSystemException $e) {
            return false;
        }
    }
}
