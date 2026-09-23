<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\GiftCardExtension\Creditmemo\ResourceModel;

use Amasty\GiftCardAccount\Api\Data\GiftCardCreditmemoInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Creditmemo extends AbstractDb
{
    public const TABLE_NAME = 'amasty_giftcard_creditmemo';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, GiftCardCreditmemoInterface::ENTITY_ID);
    }
}
