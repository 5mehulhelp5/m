<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote;

use Magento\Quote\Model\Quote\Item;

class FilteredItems
{
    /**
     * @var array
     */
    private $filteredQuoteItems = [];

    /**
     * @return array
     */
    public function getFilteredItems(): array
    {
        return $this->filteredQuoteItems;
    }

    /**
     * @param Item $item
     */
    public function setItem(Item $item): array
    {
        $this->filteredQuoteItems[$item->getId()] = $item;

        return $this->filteredQuoteItems;
    }
}
