<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Api\Action;

use Amasty\ImportCore\Api\ImportProcessInterface;

interface FileUploaderInterface
{
    /**
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    public function initialize(ImportProcessInterface $importProcess): void;

    /**
     * @param ImportProcessInterface $importProcess
     * @return void
     */
    public function execute(ImportProcessInterface $importProcess): void;
}
