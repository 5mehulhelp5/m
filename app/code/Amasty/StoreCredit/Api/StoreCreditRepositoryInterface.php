<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Api;

interface StoreCreditRepositoryInterface
{
    /**
     * @param int $customerId
     *
     * @return \Amasty\StoreCredit\Api\Data\StoreCreditInterface
     */
    public function getByCustomerId($customerId);
}
