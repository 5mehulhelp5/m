<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Calculation;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\StoreManagerInterface;

class Currency
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(StoreManagerInterface $storeManager, PriceCurrencyInterface $priceCurrency)
    {
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Directory\Model\Currency|string $currencyCode
     * @param int|null $storeId
     * @return float
     */
    public function getCurrencyRate($currencyCode, ?int $storeId = null): float
    {
        return (float)$this->storeManager->getStore($storeId)->getBaseCurrency()->getRate($currencyCode);
    }

    /**
     * @param float $price
     * @param int $precision
     * @return float
     */
    public function roundPrice(float $price, int $precision = PriceCurrencyInterface::DEFAULT_PRECISION): float
    {
        return (float)$this->priceCurrency->roundPrice($price, $precision);
    }
}
