<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\CodePool\ResourceModel;

use Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection;

class CodePoolRuleCollection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\GiftCard\Model\CodePool\CodePoolRule::class,
            \Amasty\GiftCard\Model\CodePool\ResourceModel\CodePoolRule::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
