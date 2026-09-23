<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Api;

interface ApplyStoreCreditToQuoteInterface
{
    /**
     * @param int $cartId
     * @param float $amount
     * @return float
     */
    public function apply($cartId, $amount);

    /**
     * @param int $cartId
     * @return float
     */
    public function cancel($cartId);
}
