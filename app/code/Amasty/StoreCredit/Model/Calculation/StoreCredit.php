<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Calculation;

use Amasty\StoreCredit\Model\Calculation\StoreCredit\Applier;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Total\Quote\FilteredItems;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

class StoreCredit
{
    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var FilteredItems
     */
    private $filteredItems;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Distributor
     */
    private $distributor;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var ItemAmountCalculator
     */
    private $itemAmountCalculator;

    public function __construct(
        Applier $applier,
        FilteredItems $filteredItems,
        ConfigProvider $configProvider,
        Distributor $distributor,
        Currency $currency,
        ItemAmountCalculator $itemAmountCalculator
    ) {
        $this->applier = $applier;
        $this->filteredItems = $filteredItems;
        $this->configProvider = $configProvider;
        $this->distributor = $distributor;
        $this->currency = $currency;
        $this->itemAmountCalculator = $itemAmountCalculator;
    }

    /**
     * @param Quote $quote
     * @param float $creditAmount
     * @param float $shippingAmount
     */
    public function splitStoreCreditByItemsAndShipping(Quote $quote, float $creditAmount, float $shippingAmount): void
    {
        if (!$creditAmount) {
            return;
        }

        $currencyCode = $quote->getQuoteCurrencyCode();
        $baseCreditAmount = $creditAmount / $this->currency->getCurrencyRate($currencyCode, $quote->getStoreId());

        if ($this->configProvider->isAllowOnShipping($quote->getStoreId())) {
            if ($baseCreditAmount <= $shippingAmount) {
                $this->applier->applyShippingToQuote($quote, $baseCreditAmount);

                return;
            }
            $baseCreditAmount -= $shippingAmount;
            $this->applier->applyShippingToQuote($quote, $shippingAmount);
        }

        $items = $this->filteredItems->getFilteredItems();
        if (!$items) {
            return;
        }

        usort($items, [$this, 'sortItems']);

        $allCartPrice = $this->itemAmountCalculator->getAllItemsPrice($items);
        $percent = ($baseCreditAmount * 100) / $allCartPrice;
        $itemsStoreCredit = $this->distributor->distribute($items, $baseCreditAmount, $percent);

        $this->applier->applyToQuoteItems($items, $itemsStoreCredit);
    }

    /**
     * Sorting items before apply reward points
     * cheapest should go first
     *
     * @param Item $itemA
     * @param Item $itemB
     *
     * @return int
     */
    private function sortItems(Item $itemA, Item $itemB): int
    {
        if ($this->itemAmountCalculator->calculateItemAmount($itemA)
            > $this->itemAmountCalculator->calculateItemAmount($itemB)
        ) {
            return 1;
        }

        if ($this->itemAmountCalculator->calculateItemAmount($itemA)
            < $this->itemAmountCalculator->calculateItemAmount($itemB)
        ) {
            return -1;
        }

        return 0;
    }
}
