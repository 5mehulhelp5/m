<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin\Quote\Model\Quote\Item\ToOrderItem;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order\Item as OrderItem;

class SetStoreCreditData
{
    /**
     * @see ToOrderItem::convert()
     *
     * @param ToOrderItem $subject
     * @param OrderItem $result
     * @param AbstractItem $item
     * @return OrderItem
     */
    public function afterConvert(ToOrderItem $subject, OrderItem $result, AbstractItem $item): OrderItem
    {
        $storeCredit = $item->getData(SalesFieldInterface::AMSC_AMOUNT);

        if ($storeCredit) {
            $result->setData(SalesFieldInterface::AMSC_AMOUNT, $storeCredit);
        }

        return $result;
    }
}
