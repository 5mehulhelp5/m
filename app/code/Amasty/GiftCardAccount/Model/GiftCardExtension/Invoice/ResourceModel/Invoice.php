<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardExtension\Invoice\ResourceModel;

use Amasty\GiftCardAccount\Api\Data\GiftCardInvoiceInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Invoice extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_invoice';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, GiftCardInvoiceInterface::ENTITY_ID);
    }
}
