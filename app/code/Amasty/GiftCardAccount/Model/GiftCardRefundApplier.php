<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model;

use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\OrderInterface;

class GiftCardRefundApplier
{
    /**
     * @var RefundStrategy
     */
    private $refundStrategy;

    public function __construct(
        RefundStrategy $refundStrategy
    ) {
        $this->refundStrategy = $refundStrategy;
    }

    /**
     * @param OrderInterface $order
     * @param float $totalRefundAmount
     * @param Phrase|null $message message can have 2 arguments for refunded amount value and account codes
     *
     * @return void
     */
    public function apply(OrderInterface $order, float $totalRefundAmount, ?Phrase $message = null): void
    {
        $refundedAccounts = $this->refundStrategy->refundToAccount($order, $totalRefundAmount);
        if (empty($refundedAccounts)) {
            return;
        }
        $refundedAmount = array_sum(array_column($refundedAccounts, RefundStrategy::KEY_AMOUNT));
        $refundedCodes = implode(',', array_column($refundedAccounts, RefundStrategy::KEY_CODE));
        $refundMessage = $message
            ? $message->getText()
            : __('%1 (in store\'s base currency) has been refunded to Gift Card Account(s) %2.')->getText();

        $order->addCommentToStatusHistory(__(
            $refundMessage,
            [
                $order->getBaseCurrency()->formatTxt($refundedAmount),
                $refundedCodes
            ]
        ))->setIsCustomerNotified(false);
    }
}
