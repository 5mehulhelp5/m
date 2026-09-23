<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Archive;

use Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils\DownloadableFile;

interface ArchiveAdapterInterface
{
    /**
     * @param string $destination
     * @param DownloadableFile[] $images
     *
     * @return void
     */
    public function pack(string $destination, array $images): void;

    public function getExtension(): string;
}
