<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;

class DownloaderFactory
{
    public const FILE_DOWNLOADER = 'file';
    public const ARCHIVE_DOWNLOADER = 'archive';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var string[]
     */
    private $downloadersPool;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $downloadersPool = []
    ) {
        $this->objectManager = $objectManager;
        $this->downloadersPool = $downloadersPool;
    }

    /**
     * @param string $type
     * @param array $args
     *
     * @return Downloader\AccountImageDownloaderInterface
     * @throws NotFoundException
     */
    public function create(string $type, array $args = []): Downloader\AccountImageDownloaderInterface
    {
        if (!isset($this->downloadersPool[$type])) {
            throw new NotFoundException(
                __('The "%1" downloader type isn\'t defined. Verify the downloader and try again.', $type)
            );
        }

        $downloader = $this->objectManager->create($this->downloadersPool[$type], $args);
        if (!$downloader instanceof Downloader\AccountImageDownloaderInterface) {
            throw new \LogicException(
                'The downloader instance "' . $type . '" must implement '
                . Downloader\AccountImageDownloaderInterface::class
            );
        }

        return $downloader;
    }
}
