<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Import Core for Magento 2 (System)
 */

namespace Amasty\ImportCore\Import\Config\RelationSource;

use Amasty\ImportCore\Api\Config\Relation\RelationConfigInterface;

interface RelationSourceInterface
{
    /**
     * @return RelationConfigInterface[]
     */
    public function get();
}
