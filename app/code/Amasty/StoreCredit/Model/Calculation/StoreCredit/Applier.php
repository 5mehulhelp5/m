<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Calculation\StoreCredit;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\Calculation\Currency;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class Applier
{
    /**
     * @var Currency
     */
    private $priceCurrency;

    public function __construct(Currency $priceCurrency)
    {
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Quote $quote
     * @param float $storeCredit
     */
    public function applyToQuote(Quote $quote, float $storeCredit): void
    {
        $currencyRate = $this->priceCurrency->getCurrencyRate($quote->getQuoteCurrencyCode(), $quote->getStoreId());
        $quote->setData(SalesFieldInterface::AMSC_AMOUNT, $storeCredit);
        $quote->setData(
            SalesFieldInterface::AMSC_BASE_AMOUNT,
            $this->priceCurrency->roundPrice($storeCredit / $currencyRate)
        );
    }

    /**
     * @param QuoteItem[] $items
     */
    public function applyToQuoteItems(array $items, array $itemsStoreCredit): void
    {
        /** @var Item $item */
        foreach ($items as $item) {
            $itemStoreCredit = $itemsStoreCredit[$item->getId()] ?? 0;
            $item->setData(SalesFieldInterface::AMSC_AMOUNT, (float)$itemStoreCredit);
        }
    }

    /**
     * @param Quote $quote
     * @param float $storeCredit
     */
    public function applyShippingToQuote(Quote $quote, float $storeCredit): void
    {
        $quote->setData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT, $storeCredit);
    }

    /**
     * @param Quote $quote
     */
    public function clearQuote(Quote $quote): void
    {
        $quote->setData(SalesFieldInterface::AMSC_AMOUNT, 0.0);
        $quote->setData(SalesFieldInterface::AMSC_BASE_AMOUNT, 0.0);
        $quote->setData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT, 0.0);
        $this->clearQuoteItems($quote->getAllItems());
    }

    /**
     * @param Item[] $quoteItems
     */
    public function clearQuoteItems(array $quoteItems): void
    {
        foreach ($quoteItems as $item) {
            $item->setData(SalesFieldInterface::AMSC_AMOUNT, 0.0);
        }
    }
}
