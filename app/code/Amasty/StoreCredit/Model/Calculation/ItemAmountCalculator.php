<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Calculation;

use Amasty\StoreCredit\Model\ConfigProvider;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Catalog\Model\Product\Type;

class ItemAmountCalculator
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param QuoteItem[] $items
     * @return float
     */
    public function getAllItemsPrice(array $items): float
    {
        $allItemsPrice = 0;
        /** @var QuoteItem|OrderItem $item */
        foreach ($items as $item) {
            $allItemsPrice += $this->calculateItemAmount($item);
        }

        return (float)$allItemsPrice;
    }

    /**
     * @param QuoteItem|OrderItem $item
     * @return float
     */
    public function calculateItemAmount($item): float
    {
        $originalPrice = $item->getPrice();
        if (!$originalPrice && ($item->getProductType() === Type::TYPE_VIRTUAL)) {
            $originalPrice = $item->getProduct()->getPrice();
        }
        $amount = ($originalPrice * $item->getQty()) - $item->getBaseDiscountAmount();

        if ($this->configProvider->isAllowOnTax($item->getStoreId())) {
            $amount += $item->getTaxAmount();
        }

        return (float)max(0, $amount);
    }
}
