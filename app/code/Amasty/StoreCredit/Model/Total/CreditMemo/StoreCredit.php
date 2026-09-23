<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Model\Total\CreditMemo;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\Calculation\PartialLeftCalculator;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\StoreCredit\RefundCreditStorage;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class StoreCredit extends AbstractTotal
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var PartialLeftCalculator
     */
    private $partialLeftCalculator;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var RefundCreditStorage
     */
    private $refundCreditStorage;

    public function __construct(
        ConfigProvider $configProvider,
        RequestInterface $request,
        PriceCurrencyInterface $priceCurrency,
        PartialLeftCalculator $partialLeftCalculator,
        array $data = [],
        ?State $appState = null, //todo: move to not optional
        ?RefundCreditStorage $refundCreditStorage = null //todo: move to not optional
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
        $this->partialLeftCalculator = $partialLeftCalculator;
        // OM for backward compatibility
        $this->appState = $appState ?? ObjectManager::getInstance()->get(State::class);
        $this->refundCreditStorage = $refundCreditStorage
            ?? ObjectManager::getInstance()->get(RefundCreditStorage::class);
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return $this
     * @throws LocalizedException
     */
    public function collect(Creditmemo $creditmemo)
    {
        if (!$creditmemo->getOrder()->getCustomerId()) {
            return $this;
        }
        $baseAmountEntered = $this->getBaseAmountEntered();
        if ($baseAmountEntered < 0) {
            throw new LocalizedException(__('Store Credit Refund couldn\'t be less than zero.'));
        }

        $returnToStoreCredit = $this->isReturnToStoreCredit();
        $creditmemo->setData(SalesFieldInterface::AMSC_USE, $returnToStoreCredit);

        if ($returnToStoreCredit) {
            if ($baseAmountEntered > $creditmemo->getGrandTotal()) {
                $baseAmountEntered = $creditmemo->getGrandTotal();
            }
            $this->collectAllSegments($creditmemo, $baseAmountEntered);  // checkbox "Refund to Store Credit" is checked
        } else {
            $this->collectAppliedStoreCredits($creditmemo);  // checkbox "Refund to Store Credit" is unchecked
        }

        return $this;
    }

    /**
     * @param Creditmemo $creditmemo
     * @param float|null $baseAmountEntered
     */
    public function collectAllSegments(Creditmemo $creditmemo, ?float $baseAmountEntered): void
    {
        $order = $creditmemo->getOrder();
        $storeId = (int)$order->getStoreId();
        $currencyCode = (string)$order->getOrderCurrencyCode();
        $leftBaseStoreCredit = 0;

        $leftCreditShippingAmnt = $this->getLeftCreditShippingAmount($order, $creditmemo);
        if ($baseAmountEntered !== null
            && ($this->request->getParam('amstore_credit_new')
                || $this->appState->getAreaCode() === Area::AREA_WEBAPI_REST
            )
        ) {
            $partialLeftStoreCredit = $this->partialLeftCalculator->calculatePartialStoreCredit($creditmemo);
            if ($partialLeftStoreCredit) {
                $leftBaseStoreCredit = $partialLeftStoreCredit;
            }

            $leftBaseStoreCredit += $leftCreditShippingAmnt;

            // we cannot return to credit cart (or other) more than customer paid from it
            if ($baseAmountEntered < $leftBaseStoreCredit) {
                $baseStoreCreditAmount = $leftBaseStoreCredit;
                $storeCreditAmount
                    = $this->priceCurrency->convertAndRound($leftBaseStoreCredit, $storeId, $currencyCode);
            } else {
                $baseStoreCreditAmount = $baseAmountEntered;
                $storeCreditAmount = $this->priceCurrency->convertAndRound($baseAmountEntered, $storeId, $currencyCode);
            }
        } else {
            $storeCreditAmount = $creditmemo->getGrandTotal();
            $baseStoreCreditAmount = $creditmemo->getBaseGrandTotal();
        }

        $this->setTotalsToCreditmemo($creditmemo, $baseStoreCreditAmount, $storeCreditAmount, $leftCreditShippingAmnt);
        $this->setTotalsToCreditmemoItem($creditmemo, $storeCreditAmount);
    }

    /**
     * @param Creditmemo $creditmemo
     */
    public function collectAppliedStoreCredits(Creditmemo $creditmemo): void
    {
        $order = $creditmemo->getOrder();
        $storeId = (int)$order->getStoreId();
        $currencyCode = (string)$order->getOrderCurrencyCode();
        $storeCreditAmount = $baseStoreCreditAmount = $leftBaseStoreCredit = 0;

        $partialLeftStoreCredit = $this->partialLeftCalculator->calculatePartialStoreCredit($creditmemo);
        if ($partialLeftStoreCredit) {
            $leftBaseStoreCredit = $partialLeftStoreCredit;
        }

        $leftCreditShippingAmnt = $this->getLeftCreditShippingAmount($order, $creditmemo);
        $leftBaseStoreCredit += $leftCreditShippingAmnt;

        $leftStoreCredit = $this->priceCurrency->convertAndRound($leftBaseStoreCredit, $storeId, $currencyCode);

        if ($this->isFloatEmpty($leftBaseStoreCredit)) {
            $leftStoreCredit = $leftBaseStoreCredit = 0;
        }

        if ($leftBaseStoreCredit <= $creditmemo->getBaseGrandTotal()) {
            $storeCreditAmount = $leftStoreCredit;
            $baseStoreCreditAmount = $leftBaseStoreCredit;
        }

        $this->setTotalsToCreditmemo($creditmemo, $baseStoreCreditAmount, $storeCreditAmount, $leftCreditShippingAmnt);
    }

    /**
     * @return bool
     */
    private function isReturnToStoreCredit(): bool
    {
        if ($this->appState->getAreaCode() === Area::AREA_WEBAPI_REST) {
            return true;
        }
        $returnToStoreCredit = $this->request->getParam('return_to_store_credit');
        if ($returnToStoreCredit === null) {
            $returnToStoreCredit = $this->configProvider->isRefundAutomatically();
        }

        return (bool)$returnToStoreCredit;
    }

    /**
     * @param Creditmemo $creditmemo
     * @param float $baseStoreCreditAmount
     * @param float $storeCreditAmount
     * @param float $leftCreditShippingAmount
     */
    private function setTotalsToCreditmemo(
        Creditmemo $creditmemo,
        $baseStoreCreditAmount,
        $storeCreditAmount,
        $leftCreditShippingAmount
    ) {
        $order = $creditmemo->getOrder();
        $creditmemo->setAmstorecreditAmount($storeCreditAmount);
        $creditmemo->setAmstorecreditBaseAmount($baseStoreCreditAmount);
        $creditmemo->setData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT, $leftCreditShippingAmount);

        $grandTotal = $creditmemo->getGrandTotal();
        $baseGrandTotal = $creditmemo->getBaseGrandTotal();
        $grandTotal = $grandTotal - $storeCreditAmount;
        $baseGrandTotal = $baseGrandTotal - $baseStoreCreditAmount;

        $isOrderFullyCoveredByStoreCredit = $this->isFloatEmpty($order->getBaseGrandTotal());

        if ($this->isFloatEmpty($baseGrandTotal) || $isOrderFullyCoveredByStoreCredit) {
            $grandTotal = $baseGrandTotal = 0;
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        $creditmemo->setGrandTotal($grandTotal);
        $creditmemo->setBaseGrandTotal($baseGrandTotal);
    }

    /**
     * @param Creditmemo $creditmemo
     * @param float|null $storeCreditAmount
     */
    private function setTotalsToCreditmemoItem(Creditmemo $creditmemo, ?float $storeCreditAmount)
    {
        $order = $creditmemo->getOrder();
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $order->getItemById($creditmemoItem->getOrderItemId());
            if ($orderItem && $creditmemoItem->getQty() > 0) {
                $creditmemoItem->setData(SalesFieldInterface::AMSC_AMOUNT, $storeCreditAmount);
            }
        }
    }

    /**
     * @param Order $order
     * @param Creditmemo $creditmemo
     * @return float
     */
    private function getLeftCreditShippingAmount(Order $order, Creditmemo $creditmemo): float
    {
        $leftCreditShippingAmount = 0.0;
        if ($order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT) > $creditmemo->getBaseShippingAmount()) {
            $leftCreditShippingAmount = $creditmemo->getBaseShippingAmount();
        } else {
            $leftCreditShippingAmount += $order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT);
        }

        return $leftCreditShippingAmount;
    }

    /**
     * @param float $value
     * @return bool
     */
    private function isFloatEmpty($value)
    {
        return $value < 0.0001;
    }

    private function getBaseAmountEntered(): float
    {
        if ($this->appState->getAreaCode() === Area::AREA_WEBAPI_REST) {
            return $this->refundCreditStorage->getBaseAmount();
        }

        return (float)$this->request->getParam('store_credit_return_amount');
    }
}
