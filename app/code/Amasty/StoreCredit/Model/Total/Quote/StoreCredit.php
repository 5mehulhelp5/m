<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Quote;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Api\StoreCreditRepositoryInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\Total\Quote\Collectors\QuoteCollector;
use Magento\Framework\App\State;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var QuoteCollector
     */
    private $quoteCollectorPool;

    /**
     * @var StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    public function __construct(
        State $state,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        QuoteCollector $quoteCollectorPool,
        StoreCreditRepositoryInterface $storeCreditRepository
    ) {
        $this->setCode('amstorecredit');
        $this->state = $state;
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
        $this->quoteCollectorPool = $quoteCollectorPool;
        $this->storeCreditRepository = $storeCreditRepository;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        if ($this->configProvider->isEnabled()
            && $quote->getCustomerId()
            && $quote->getBaseToQuoteRate()
            && $total->getGrandTotal() > 0
        ) {
            $items = $shippingAssignment->getItems();
            $availableBaseCredit = $this->storeCreditRepository->getByCustomerId($quote->getCustomerId())
                ->getStoreCredit();

            if (!$items || !$availableBaseCredit) {
                return $this;
            }

            $storeId = $quote->getStoreId();
            $currency = $quote->getQuoteCurrencyCode();
            $availableCredit = $this->priceCurrency->convertAndRound($availableBaseCredit, $storeId, $currency);

            $collector = $this->quoteCollectorPool->get($this->state->getAreaCode());
            $collector->collect($quote, $shippingAssignment, $total, $availableCredit);

            $this->calculateGrandTotal($quote, $total);
        }

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     * @return array|null
     */
    public function fetch(Quote $quote, Total $total)
    {
        if ($this->configProvider->isEnabled()) {
            if ($quote->getData(SalesFieldInterface::AMSC_USE)) {
                return [
                    'code' => $this->getCode(),
                    'title' => __('Store Credit'),
                    'value' => -$quote->getData(SalesFieldInterface::AMSC_AMOUNT)
                ];
            }

            return [
                'code' => $this->getCode() . '_max',
                'title' => __('Store Credit Max'),
                'value' => $quote->getData(SalesFieldInterface::AMSC_AMOUNT)
            ];
        }

        return null;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     */
    private function calculateGrandTotal(Quote $quote, Total $total): void
    {
        if ($quote->getData(SalesFieldInterface::AMSC_USE) && $quote->getAmstorecreditBaseAmount()) {
            $grandTotal = $total->getGrandTotal() - $quote->getAmstorecreditAmount();
            $grandBaseTotal = $total->getBaseGrandTotal() - $quote->getAmstorecreditBaseAmount();
            if ($grandTotal < 0.0001) {
                $grandTotal = $grandBaseTotal = 0;
            }

            $total->setGrandTotal($grandTotal);
            $total->setBaseGrandTotal($grandBaseTotal);
        } else {
            $quote->setData(SalesFieldInterface::AMSC_USE, 0);
        }
    }
}
