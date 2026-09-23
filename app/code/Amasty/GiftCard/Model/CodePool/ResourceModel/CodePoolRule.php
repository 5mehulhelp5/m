<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model\CodePool\ResourceModel;

use Amasty\GiftCard\Api\Data\CodePoolRuleInterface;
use Magento\Rule\Model\ResourceModel\AbstractResource;

class CodePoolRule extends AbstractResource
{
    public const TABLE_NAME = 'amasty_giftcard_code_pool_rule';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, CodePoolRuleInterface::RULE_ID);
    }
}
