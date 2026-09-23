<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\FileDestination\Type\ServerFile;

interface ConfigInterface
{
    /**
     * @return string|null
     */
    public function getFilepath(): ?string;

    /**
     * @param string|null $filepath
     *
     * @return \Amasty\ExportCore\Export\FileDestination\Type\ServerFile\ConfigInterface
     */
    public function setFilepath(?string $filepath): ConfigInterface;

    /**
     * @return string|null
     */
    public function getFilename(): ?string;

    /**
     * @param string|null $filename
     *
     * @return \Amasty\ExportCore\Export\FileDestination\Type\ServerFile\ConfigInterface
     */
    public function setFilename(?string $filename): ConfigInterface;
}
