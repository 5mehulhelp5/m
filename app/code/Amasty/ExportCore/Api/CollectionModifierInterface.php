<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Export Core for Magento 2 (System)
 */

namespace Amasty\ExportCore\Api;

use Amasty\ExportCore\Api\ExportProcessInterface;

interface CollectionModifierInterface
{
    public function apply(\Magento\Framework\Data\Collection $collection)
        : \Amasty\ExportCore\Api\CollectionModifierInterface;
}
