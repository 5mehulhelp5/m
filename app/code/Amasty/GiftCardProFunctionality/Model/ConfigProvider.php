<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Pro Functionality for Magento 2 (System)
 */

namespace Amasty\GiftCardProFunctionality\Model;

use Amasty\GiftCard\Model\ConfigProvider as GiftCardConfigProvider;

class ConfigProvider extends GiftCardConfigProvider
{
    public const XPATH_ALLOW_CART_PRICE_RULE_FOR_AM_GIFT_CARD = 'general/allow_cart_price_rule_for_am_gift_card';

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isCartPriceRuleForAmGiftCard($storeId = null): bool
    {
        return (bool)$this->getValue(self::XPATH_ALLOW_CART_PRICE_RULE_FOR_AM_GIFT_CARD, $storeId);
    }
}
