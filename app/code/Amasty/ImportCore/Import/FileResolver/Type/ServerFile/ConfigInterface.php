<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\FileResolver\Type\ServerFile;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getFilename(): string;

    /**
     * @param $filename
     *
     * @return void
     */
    public function setFilename(string $filename): void;
}
