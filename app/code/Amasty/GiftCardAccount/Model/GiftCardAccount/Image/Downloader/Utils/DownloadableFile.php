<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\Image\Downloader\Utils;

class DownloadableFile
{
    /**
     * @var string|null
     */
    private $path;

    /**
     * @var string|null
     */
    private $fileName;

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return (string)$this->path;
    }

    public function setFileName(string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return (string)$this->fileName;
    }
}
