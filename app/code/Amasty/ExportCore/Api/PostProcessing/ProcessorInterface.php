<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\PostProcessing;

use Amasty\ExportCore\Api\ExportProcessInterface;

interface ProcessorInterface
{
    public function process(ExportProcessInterface $exportProcess): ProcessorInterface;
}
