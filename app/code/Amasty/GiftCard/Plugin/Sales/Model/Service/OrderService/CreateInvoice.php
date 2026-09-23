<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Plugin\Sales\Model\Service\OrderService;

use Amasty\GiftCard\Model\ConfigProvider;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Psr\Log\LoggerInterface;

class CreateInvoice
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ConfigProvider $configProvider,
        InvoiceService $invoiceService,
        Transaction $transaction,
        LoggerInterface $logger
    ) {
        $this->configProvider = $configProvider;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->logger = $logger;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result,
        OrderInterface $order
    ): OrderInterface {
        if ($this->isOrderInvoiceValid($order)) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice->getTotalQty()) {
                return $result;
            }

            try {
                $invoice->register();
                $invoice->getOrder()->setCustomerNoteNotify(false);
                $invoice->getOrder()->setIsInProcess(true);
                $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        return $result;
    }

    private function isOrderInvoiceValid(OrderInterface $order): bool
    {
        return $order->getGrandTotal() === 0.0
            && $this->configProvider->isEnabled($order->getStoreId())
            && $this->configProvider->isAutoChangeOrderStatusAllowed()
            && $order->canInvoice()
            && $order->getExtensionAttributes()
            && $order->getExtensionAttributes()->getAmGiftcardOrder()
            && !empty($order->getExtensionAttributes()->getAmGiftcardOrder()->getAppliedAccounts());
    }
}
