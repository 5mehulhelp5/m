<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin\Sales\Model\RefundInvoice;

use Amasty\StoreCredit\Api\ManageCustomerStoreCreditInterface;
use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\History\MessageProcessor;
use Amasty\StoreCredit\Model\StoreCredit\RefundCreditStorage;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Model\RefundInvoice;

class AddStoreCredit
{
    /**
     * @var RefundCreditStorage
     */
    private $refundCreditStorage;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ManageCustomerStoreCreditInterface
     */
    private $manageCustomerStoreCredit;

    public function __construct(
        ConfigProvider $configProvider,
        RefundCreditStorage $refundCreditStorage,
        ManageCustomerStoreCreditInterface $manageCustomerStoreCredit
    ) {
        $this->configProvider = $configProvider;
        $this->refundCreditStorage = $refundCreditStorage;
        $this->manageCustomerStoreCredit = $manageCustomerStoreCredit;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        RefundInvoice $subject,
        $result,
        $invoiceId,
        array $items = [],
        $isOnline = false,
        $notify = false,
        $appendComment = false,
        ?CreditmemoCommentCreationInterface $comment = null,
        ?CreditmemoCreationArgumentsInterface $arguments = null
    ) {
        if ($this->configProvider->isEnabled()) {
            $creditMemo = $this->refundCreditStorage->getCurrentCreditMemo();
            if ($amount = $creditMemo->getAmstorecreditBaseAmount()) {
                $order = $creditMemo->getOrder();
                $this->manageCustomerStoreCredit->addOrSubtractStoreCredit(
                    $creditMemo->getCustomerId(),
                    $amount,
                    MessageProcessor::CREDIT_MEMO_REFUND,
                    [$order->getIncrementId()],
                    $creditMemo->getStoreId(),
                    '',
                    false,
                    (int)$order->getEntityId()
                );
            }
        }

        return $result;
    }
}
