<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\FileResolver\Type\UploadFile;

interface ConfigInterface
{
    /**
     * @return string
     */
    public function getHash(): string;

    /**
     * @param string $hash
     *
     * @return void
     */
    public function setHash(string $hash): void;
}
