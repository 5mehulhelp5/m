<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Store Credit & Refund for Magento 2
 */

namespace Amasty\StoreCredit\Plugin\Sales\Model\RefundInvoice;

use Amasty\StoreCredit\Model\ConfigProvider;
use Amasty\StoreCredit\Model\StoreCredit\RefundCreditStorage;
use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Model\RefundInvoice;

class SaveEnteredAmount
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

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(
        RefundInvoice $subject,
        int $invoiceId,
        array $items = [],
        bool $isOnline = false,
        $notify = false,
        $appendComment = false,
        ?CreditmemoCommentCreationInterface $comment = null,
        ?CreditmemoCreationArgumentsInterface $arguments = null
    ) {
        if ($this->configProvider->isEnabled() && $arguments) {
            $extensionAttributes = $arguments->getExtensionAttributes();
            $this->refundCreditStorage->setBaseAmount((float)$extensionAttributes->getAmstorecreditBaseAmount());
        }

        return null;
    }
}
