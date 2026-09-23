<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\FileDestination\Type\ServerFile;

class Config implements ConfigInterface
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $filepath;

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(?string $filepath): ConfigInterface
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): ConfigInterface
    {
        $this->filename = $filename;

        return $this;
    }
}
