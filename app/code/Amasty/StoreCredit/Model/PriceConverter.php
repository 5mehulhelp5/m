<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model;

use Magento\Directory\Model\Currency;
use Magento\Store\Model\StoreManagerInterface;

class PriceConverter
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Currency
     */
    private $currency;

    public function __construct(
        Currency $currency,
        StoreManagerInterface $storeManager
    ) {
        $this->currency = $currency;
        $this->storeManager = $storeManager;
    }

    public function formatPrice(float $amount): string
    {
        $store = $this->storeManager->getStore();
        $currencyCode = $store->getBaseCurrency()->getCurrencyCode();

        return $this->currency->load($currencyCode)->format($amount, [], false);
    }
}
