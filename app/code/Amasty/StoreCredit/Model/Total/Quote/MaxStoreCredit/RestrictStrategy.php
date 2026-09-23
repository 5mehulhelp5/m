<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote\MaxStoreCredit;

use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Source\RestrictAction;
use Amasty\StoreCredit\Model\Total\Quote\FilteredItems;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class RestrictStrategy implements RetrieveStrategyInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var FilteredItems
     */
    private $filteredItems;

    public function __construct(
        ConfigProvider $configProvider,
        FilteredItems $filteredItems
    ) {
        $this->configProvider = $configProvider;
        $this->filteredItems = $filteredItems;
    }

    public function execute(Quote $quote, Total $total): float
    {
        $result = 0.0;
        $hasApplicableItems = false;

        /** @var QuoteItem $quoteItem */
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($quoteItem->getHasChildren() && $quoteItem->isChildrenCalculated()) {
                continue;
            }

            if ($this->isApplicableForItem($quoteItem)) {
                $hasApplicableItems = true;
                $result += $quoteItem->getRowTotal() - $quoteItem->getDiscountAmount();
                if ($this->configProvider->isAllowOnTax($quote->getStoreId())) {
                    $result += $quoteItem->getTaxAmount();
                }
                $this->filteredItems->setItem($quoteItem);
                $result += $quoteItem->getWeeeTaxAppliedRowAmountInclTax();
            }
        }

        if ($this->configProvider->isAllowOnShipping($quote->getStoreId())) {
            $result += $total->getShippingAmount();
        }
        if ($this->configProvider->isAllowOnTax($quote->getStoreId())) {
            $result += $total->getShippingTaxAmount();
        }

        // GiftCard by Amasty compatibility
        if ($total->getAmGiftCardsAmount() && $hasApplicableItems === true) {
            $result -= $total->getAmGiftCardsAmount();
            $result = max($result, 0.0);
        }

        return $result;
    }

    private function isApplicableForItem(QuoteItem $quoteItem): bool
    {
        $product = $quoteItem->getProduct();

        $productSkusForRestrict = $this->configProvider->getProductSkusForRestrict();
        $categoryIdsForRestrict = $this->configProvider->getCategoryIdsForRestrict();

        if ($productSkusForRestrict || $categoryIdsForRestrict) {
            $result = !empty(array_intersect($this->getApplicableSkus($quoteItem), $productSkusForRestrict))
                || !empty(array_intersect($product->getCategoryIds(), $categoryIdsForRestrict));

            if ($this->configProvider->getRestrictAction() === RestrictAction::EXCLUDE) {
                $result = !$result;
            }
        } else {
            $result = true;
        }

        return $result;
    }

    private function getApplicableSkus(QuoteItem $quoteItem): array
    {
        $skus = [$quoteItem->getProduct()->getData('sku')];

        if ($quoteItem->getProductType() === Configurable::TYPE_CODE) {
            $option = $quoteItem->getOptionByCode('simple_product');
            $product = $option->getProduct();
            $skus[] = $product->getData('sku');
        }

        if ($quoteItem->getParentItem()) {
            $skus[] = $quoteItem->getParentItem()->getProduct()->getData('sku');
        }

        return $skus;
    }
}
