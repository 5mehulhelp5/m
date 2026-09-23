<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Observer\Admin;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetRequestedCreditData implements ObserverInterface
{
    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $quote = $observer->getOrderCreateModel()->getQuote();

        if (isset($request['am_use_store_credit'])) {
            $quote->setData(SalesFieldInterface::AMSC_USE, (int)$request['am_use_store_credit']);
        }

        if (isset($request['am_store_credit_amount'])) {
            $quote->setData(SalesFieldInterface::AMSC_AMOUNT, abs((float)$request['am_store_credit_amount']));
        }
    }
}
