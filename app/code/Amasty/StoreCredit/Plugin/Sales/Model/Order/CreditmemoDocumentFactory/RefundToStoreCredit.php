<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin\Sales\Model\Order\CreditmemoDocumentFactory;

use Amasty\StoreCredit\Api\Data\SalesFieldInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\StoreCredit\RefundCreditStorage;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\CreditmemoDocumentFactory;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RefundToStoreCredit
{
    /**
     * @var RefundCreditStorage
     */
    private $refundCreditStorage;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        RefundCreditStorage $refundCreditStorage
    ) {
        $this->configProvider = $configProvider;
        $this->refundCreditStorage = $refundCreditStorage;
    }

    public function afterCreateFromOrder(
        CreditmemoDocumentFactory $subject,
        CreditmemoInterface $result,
        OrderInterface $order,
        array $items = [],
        ?CreditmemoCommentCreationInterface $comment = null,
        bool $appendComment = false,
        ?CreditmemoCreationArgumentsInterface $arguments = null
    ): CreditmemoInterface {
        $this->checkAmstorecreditAmount($result, $order);

        return $result;
    }

    public function afterCreateFromInvoice(
        CreditmemoDocumentFactory $subject,
        CreditmemoInterface $result,
        InvoiceInterface $invoice,
        array $items = [],
        ?CreditmemoCommentCreationInterface $comment = null,
        bool $appendComment = false,
        ?CreditmemoCreationArgumentsInterface $arguments = null
    ): CreditmemoInterface {
        $this->checkAmstorecreditAmount($result);

        return $result;
    }

    private function checkAmstorecreditAmount(CreditmemoInterface $creditMemo): void
    {
        if ($this->configProvider->isEnabled()) {
            $order = $creditMemo->getOrder();
            if ($amount = $creditMemo->getAmstorecreditAmount()) {
                $order->setAmstorecreditRefundedAmount($order->getAmstorecreditRefundedAmount() + $amount);
                $order->setAmstorecreditRefundedBaseAmount(
                    $order->getAmstorecreditRefundedBaseAmount() + $creditMemo->getAmstorecreditBaseAmount()
                );
            }
            if ($shippingAmount = $creditMemo->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT)) {
                $order->setData(
                    SalesFieldInterface::AMSC_SHIPPING_AMOUNT_REFUNDED,
                    $order->getData(SalesFieldInterface::AMSC_SHIPPING_AMOUNT_REFUNDED) + $shippingAmount
                );
            }

            //to set 'closed' status when all money are refunded to store credit.
            // @see \Magento\Sales\Model\ResourceModel\Order\Handler\State::check()
            $order->setIsInProcess(true);

            $this->refundCreditStorage->setCurrentCreditMemo($creditMemo);
        }
    }
}
