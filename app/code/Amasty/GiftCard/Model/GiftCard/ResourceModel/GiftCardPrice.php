<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\GiftCard\ResourceModel;

use Amasty\GiftCard\Api\Data\GiftCardPriceInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class GiftCardPrice extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_price';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, GiftCardPriceInterface::PRICE_ID);
    }
}
