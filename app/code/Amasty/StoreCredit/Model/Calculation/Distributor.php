<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Calculation;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;

class Distributor
{
    /**
     * @var ItemAmountCalculator
     */
    private $itemAmountCalculator;

    /**
     * @var PriceCurrencyInterface
     */
    private $currency;

    public function __construct(
        ItemAmountCalculator $itemAmountCalculator,
        PriceCurrencyInterface $currency
    ) {
        $this->itemAmountCalculator = $itemAmountCalculator;
        $this->currency = $currency;
    }

    /**
     * @param QuoteItem[] $items
     * @param float $amountToDistribute
     * @param float $percent
     * @return array where key - item ID, value - item amount value
     */
    public function distribute(array $items, float $amountToDistribute, float $percent): array
    {
        $itemsAmount = [];

        foreach ($items as $item) {
            $itemPrice = $this->itemAmountCalculator->calculateItemAmount($item);
            $amount = $this->currency->roundPrice(($itemPrice * $percent) / 100, 2);
            $amountToDistribute -= $amount;
            $itemsAmount[$item->getId()] = $amount;
            $lastItemId = $item->getId();
        }

        if (($amountToDistribute !== 0.0) && isset($lastItemId)) {
            $itemsAmount[$lastItemId] += $amountToDistribute;
        }

        return $itemsAmount;
    }
}
