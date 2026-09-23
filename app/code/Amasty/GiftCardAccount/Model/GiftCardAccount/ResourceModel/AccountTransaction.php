<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardAccount\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AccountTransaction extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_account_transaction';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'transaction_id');
    }
}
