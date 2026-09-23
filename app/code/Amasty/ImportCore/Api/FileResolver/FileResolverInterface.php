<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\FileResolver;

use Amasty\ImportCore\Api\ImportProcessInterface;

interface FileResolverInterface
{
    /**
     * Resolves import source file
     *
     * @param ImportProcessInterface $importProcess
     * @return string File path
     */
    public function execute(ImportProcessInterface $importProcess): string;
}
