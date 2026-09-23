<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote\MaxStoreCredit;

use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Total\Quote\FilteredItems;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class FullStrategy implements RetrieveStrategyInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FilteredItems
     */
    private $filteredItems;

    public function __construct(ConfigProvider $configProvider, FilteredItems $filteredItems)
    {
        $this->configProvider = $configProvider;
        $this->filteredItems = $filteredItems;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return float
     */
    public function execute(Quote $quote, Total $total): float
    {
        $maxStoreCredit = $total->getGrandTotal();
        if (!$this->configProvider->isAllowOnShipping($quote->getStoreId())) {
            $maxStoreCredit -= $total->getShippingAmount();
        }

        if (!$this->configProvider->isAllowOnTax($quote->getStoreId())) {
            $maxStoreCredit -= $total->getTaxAmount();
        }

        foreach ($quote->getAllItems() as $quoteItem) {
            $this->filteredItems->setItem($quoteItem);
        }

        return (float) $maxStoreCredit;
    }
}
