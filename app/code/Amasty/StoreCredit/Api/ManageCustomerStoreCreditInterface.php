<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Api;

use Amasty\StoreCredit\Api\Data\StoreCreditInterface;

interface ManageCustomerStoreCreditInterface
{
    /**
     * @param int $customerId
     * @param float $amount
     * @param int $action
     * @param array $actionData
     * @param int $storeId
     * @param string $message
     * @param bool $visibleForCustomer
     *
     * @return StoreCreditInterface
     */
    public function addOrSubtractStoreCredit(
        $customerId,
        $amount,
        $action,
        $actionData = [],
        $storeId = 0,
        $message = '',
        bool $visibleForCustomer = false,
        ?int $orderId = null
    );
}
