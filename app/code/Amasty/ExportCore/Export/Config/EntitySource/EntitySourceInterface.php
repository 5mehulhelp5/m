<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\EntitySource;

use Amasty\ExportCore\Api\Config\EntityConfigInterface;

interface EntitySourceInterface
{
    /**
     * @return EntityConfigInterface[]
     */
    public function get();
}
