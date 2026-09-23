<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api\FileDestination;

use Amasty\ExportCore\Api\ExportProcessInterface;

interface FileDestinationInterface
{
    public function execute(ExportProcessInterface $exportProcess);
}
