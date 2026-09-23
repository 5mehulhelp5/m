<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Config\Entity;

/**
 * Entity file uploader config
 */
interface FileUploaderConfigInterface
{
    /**
     * @return string
     */
    public function getFileUploaderClass();

    /**
     * @param string $class
     * @return void
     */
    public function setFileUploaderClass(string $class): void;

    /**
     * @param string $storagePath
     * @return void
     */
    public function setStoragePath(string $storagePath): void;

    /**
     * @return string
     */
    public function getStoragePath(): string;

    /**
     * Get file uploader instance
     *
     * @return \Amasty\ImportCore\Api\Action\FileUploaderInterface|mixed
     */
    public function getFileUploader();
}
