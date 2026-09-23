<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\CustomerCard\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\GiftCardAccount\Model\CustomerCard\CustomerCard::class,
            \Amasty\GiftCardAccount\Model\CustomerCard\ResourceModel\CustomerCard::class
        );
        $this->_setIdFieldName($this->getResource()->getIdFieldName());
    }
}
