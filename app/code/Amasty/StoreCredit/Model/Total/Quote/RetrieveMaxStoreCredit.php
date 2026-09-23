<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote;

use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Total\Quote\MaxStoreCredit\RetrieveStrategyPool;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

class RetrieveMaxStoreCredit
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RetrieveStrategyPool
     */
    private $retrieveStrategyPool;

    public function __construct(ConfigProvider $configProvider, RetrieveStrategyPool $retrieveStrategyPool)
    {
        $this->configProvider = $configProvider;
        $this->retrieveStrategyPool = $retrieveStrategyPool;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return float
     */
    public function execute(Quote $quote, Total $total): float
    {
        $retriever = $this->retrieveStrategyPool->get((int) $this->configProvider->isRestrictProductsEnabled());
        return $retriever->execute($quote, $total);
    }
}
