<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\StoreCredit\ResourceModel;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StoreCredit extends AbstractDb
{
    public const TABLE_NAME = 'amasty_store_credit';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, StoreCreditInterface::STORE_CREDIT_ID);
    }
}
