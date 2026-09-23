<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Quote\Model\Quote;

class ResetStoreCreditAfterItemDelete
{
    public function afterRemoveItem(Quote $quote, Quote $result)
    {
        $allDeleted = true;
        foreach ($result->getItemsCollection() as $item) {
            if (!$item->isDeleted() || ($item->isDeleted() && count($result->getAllItems()))) {
                $allDeleted = false;
                break;
            }
        }
        if ($allDeleted) {
            $result->setData(SalesFieldInterface::AMSC_USE, 0);
            $result->setData(SalesFieldInterface::AMSC_AMOUNT, null);
            $result->setData(SalesFieldInterface::AMSC_BASE_AMOUNT, null);
        }

        return $result;
    }
}
