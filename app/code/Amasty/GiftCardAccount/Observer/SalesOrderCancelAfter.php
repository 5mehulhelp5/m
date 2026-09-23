<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Observer;

use Amasty\GiftCardAccount\Model\GiftCardExtension\GiftCardExtensionResolver;
use Amasty\GiftCardAccount\Model\GiftCardRefundApplier;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class SalesOrderCancelAfter implements ObserverInterface
{
    /**
     * @var GiftCardRefundApplier
     */
    private $giftCardRefundApplier;

    /**
     * @var GiftCardExtensionResolver
     */
    private $gCardExtensionResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        GiftCardRefundApplier $giftCardRefundApplier,
        GiftCardExtensionResolver $gCardExtensionResolver,
        LoggerInterface $logger
    ) {
        $this->giftCardRefundApplier = $giftCardRefundApplier;
        $this->gCardExtensionResolver = $gCardExtensionResolver;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        try {
            $this->refundGiftCardAccounts($order);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }

    /**
     * @param OrderInterface|Order $order
     *
     * @return void
     */
    private function refundGiftCardAccounts(OrderInterface $order): void
    {
        $gCardOrder = $this->gCardExtensionResolver->resolve($order);
        if (!$gCardOrder || $gCardOrder->getBaseGiftAmount() < .0) {
            return;
        }
        $this->giftCardRefundApplier->apply(
            $order,
            $gCardOrder->getBaseGiftAmount(),
            __('%1 (in store\'s base currency) has been returned to Gift Card Account(s) %2 due to order cancellation')
        );
    }
}
