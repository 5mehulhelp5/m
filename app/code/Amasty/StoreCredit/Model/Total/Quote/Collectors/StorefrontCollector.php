<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote\Collectors;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\Calculation\StoreCredit as StoreCreditCalculation;
use Amasty\StoreCredit\Model\Calculation\StoreCredit\Applier;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Total\Quote\RetrieveMaxStoreCredit;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;

class StorefrontCollector implements QuoteCollectorInterface
{
    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RetrieveMaxStoreCredit
     */
    private $retrieveMaxStoreCredit;

    /**
     * @var StoreCreditCalculation
     */
    private $storeCreditCalculation;

    public function __construct(
        Applier $applier,
        ConfigProvider $configProvider,
        RetrieveMaxStoreCredit $retrieveMaxStoreCredit,
        StoreCreditCalculation $storeCreditCalculation
    ) {
        $this->applier = $applier;
        $this->configProvider = $configProvider;
        $this->retrieveMaxStoreCredit = $retrieveMaxStoreCredit;
        $this->storeCreditCalculation = $storeCreditCalculation;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @param float $availableCredit
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total,
        float $availableCredit
    ): void {
        $baseShippingAmnt = (float)$total->getBaseShippingAmount();
        if (!$quote->getData(SalesFieldInterface::AMSC_USE)) {
            $this->applier->clearQuote($quote);
        }
        $storeCredit = (float)$quote->getData(SalesFieldInterface::AMSC_AMOUNT) ?: 0.0;

        $storeCredit = $this->getResultStoreCredit($quote, $total, $storeCredit, $availableCredit);

        $this->applier->applyToQuote($quote, $storeCredit);
        if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
            $this->storeCreditCalculation->splitStoreCreditByItemsAndShipping($quote, $storeCredit, $baseShippingAmnt);
        }
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @param float $requestedStoreCredit
     * @param float $availableCredit
     * @return float
     */
    private function getResultStoreCredit(
        Quote $quote,
        Total $total,
        float $requestedStoreCredit,
        float $availableCredit
    ): float {
        if ($requestedStoreCredit > $availableCredit) {
            $requestedStoreCredit = $availableCredit;
        }

        $maxStoreCredit = $this->retrieveMaxStoreCredit->execute($quote, $total);
        if ($requestedStoreCredit > $maxStoreCredit) {
            $requestedStoreCredit = $maxStoreCredit;
        }

        if ($requestedStoreCredit <= 0 && $this->configProvider->isEncourage()) {
            $requestedStoreCredit = min($maxStoreCredit, $availableCredit);
            $quote->setData(SalesFieldInterface::AMSC_USE, 0);
        }

        return $requestedStoreCredit;
    }
}
