<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\EntitySource;

use Amasty\ImportCore\Api\Config\EntityConfigInterface;

interface EntitySourceInterface
{
    /**
     * @return EntityConfigInterface[]
     */
    public function get();
}
