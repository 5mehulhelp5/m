<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model;

use Amasty\GiftCardAccount\Model\Config\Source\CheckoutPosition;
use Amasty\GiftCardAccount\Model\Config\Source\CheckoutViewType;
use Magento\Catalog\Helper\Data;

class ConfigProvider extends \Amasty\GiftCard\Model\ConfigProvider
{
    /**#@+
     * Constants defined for xpath of system configuration
     */
    public const XPATH_CHECKOUT_POSITION = 'gift_card_account/checkout_position';
    public const XPATH_CHECKOUT_VIEW = 'gift_card_account/checkout_view_type';
    public const XPATH_REFUND_ALLOWED = 'general/allow_refund_for_orders';
    public const XPATH_MAX_GIFT_CARD_QTY = 'gift_card_account/gift_card_limit';
    public const XPATH_MULTIPLE_WEBSITES_ALLOWED = 'general/allow_to_use_on_multiple_websites';
    /**#@-*/

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getCouponCheckoutPosition($storeId = null): int
    {
        return (int)$this->getValue(self::XPATH_CHECKOUT_POSITION, $storeId)
            ?? CheckoutPosition::CHECKOUT_DISCOUNTS;
    }

    /**
     * @param int|null $storeId
     * @return int
     */
    public function getCouponCheckoutView($storeId = null): int
    {
        return (int)$this->getValue(self::XPATH_CHECKOUT_VIEW, $storeId)
            ?? CheckoutViewType::DROPDOWN;
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isRefundAllowed($storeId = null): bool
    {
        return (bool)$this->getValue(self::XPATH_REFUND_ALLOWED, $storeId);
    }

    public function getMaxGiftCardQty(?int $storeId = null): int
    {
        return (int)$this->getValue(self::XPATH_MAX_GIFT_CARD_QTY, $storeId);
    }

    public function isMultipleWebsitesAllowed(): bool
    {
        return $this->isSetGlobalFlag(self::XPATH_MULTIPLE_WEBSITES_ALLOWED)
            && (int)$this->scopeConfig->getValue(Data::XML_PATH_PRICE_SCOPE) !== Data::PRICE_SCOPE_WEBSITE;
    }
}
