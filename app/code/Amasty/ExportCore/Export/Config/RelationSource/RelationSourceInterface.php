<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Export\Config\RelationSource;

use Amasty\ExportCore\Api\Config\Relation\RelationInterface;

interface RelationSourceInterface
{
    /**
     * @return RelationInterface[]
     */
    public function get();
}
