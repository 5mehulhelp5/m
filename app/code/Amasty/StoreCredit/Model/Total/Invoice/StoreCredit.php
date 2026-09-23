<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\Invoice;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\Calculation\PartialLeftCalculator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    /**
     * @var PartialLeftCalculator
     */
    private $partialLeftCalculator;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    public function __construct(
        PartialLeftCalculator $partialLeftCalculator,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        parent::__construct($data);
        $this->partialLeftCalculator = $partialLeftCalculator;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param Invoice $invoice
     * @return $this
     */
    public function collect(Invoice $invoice): StoreCredit
    {
        $order = $invoice->getOrder();
        if ($order->getAmstorecreditAmount() > 0) {
            $storeId = (int)$order->getStoreId();
            $currencyCode = (string)$order->getOrderCurrencyCode();
            $leftBaseStoreCredit = 0;
            $invoiceGrandTotal = $invoice->getGrandTotal();
            $invoiceBaseGrandTotal = $invoice->getBaseGrandTotal();

            $partialLeftStoreCredit = $this->partialLeftCalculator->calculatePartialStoreCredit($invoice);
            if ($partialLeftStoreCredit) {
                $leftBaseStoreCredit = $partialLeftStoreCredit;
            }

            if (!$order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT_INVOICED)) {
                $leftBaseStoreCredit += $order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT);
            }

            $leftStoreCredit = $this->priceCurrency->convertAndRound($leftBaseStoreCredit, $storeId, $currencyCode);
            if ($leftBaseStoreCredit > $invoiceBaseGrandTotal) {
                $invoice->setAmstorecreditAmount($invoiceGrandTotal);
                $invoice->setAmstorecreditBaseAmount($invoiceBaseGrandTotal);
                $invoiceGrandTotal = $invoiceBaseGrandTotal = 0;
            } else {
                $invoiceGrandTotal -= $leftStoreCredit;
                $invoice->setAmstorecreditAmount($leftStoreCredit);
                $invoiceBaseGrandTotal -= $leftBaseStoreCredit;
                $invoice->setAmstorecreditBaseAmount($leftBaseStoreCredit);
                $orderCreditShippingAmount = $order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT);
                $order->setData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT_INVOICED, $orderCreditShippingAmount);
                $invoice->setData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT, $orderCreditShippingAmount);
            }

            if ($invoiceGrandTotal < 0.0001) {
                $invoiceGrandTotal = $invoiceBaseGrandTotal = 0;
            }

            $order->setAmstorecreditInvoicedBaseAmount(
                $order->getAmstorecreditInvoicedBaseAmount() + $invoice->getAmstorecreditBaseAmount()
            );

            $order->setAmstorecreditInvoicedAmount(
                $order->getAmstorecreditInvoicedAmount() + $invoice->getAmstorecreditAmount()
            );

            $invoice->setBaseGrandTotal($invoiceBaseGrandTotal);
            $invoice->setGrandTotal($invoiceGrandTotal);
        }

        return $this;
    }
}
