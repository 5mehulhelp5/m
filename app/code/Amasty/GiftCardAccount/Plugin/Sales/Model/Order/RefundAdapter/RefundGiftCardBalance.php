<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Plugin\Sales\Model\Order\RefundAdapter;

use Amasty\GiftCardAccount\Model\ConfigProvider;
use Amasty\GiftCardAccount\Model\GiftCardExtension\GiftCardExtensionResolver;
use Amasty\GiftCardAccount\Model\GiftCardRefundApplier;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\RefundAdapterInterface;
use Psr\Log\LoggerInterface;

class RefundGiftCardBalance
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

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
        ConfigProvider $configProvider,
        GiftCardRefundApplier $giftCardRefundApplier,
        GiftCardExtensionResolver $gCardExtensionResolver,
        LoggerInterface $logger
    ) {
        $this->configProvider = $configProvider;
        $this->giftCardRefundApplier = $giftCardRefundApplier;
        $this->gCardExtensionResolver = $gCardExtensionResolver;
        $this->logger = $logger;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRefund(
        RefundAdapterInterface $subjecct,
        OrderInterface $order,
        CreditmemoInterface $creditmemo
    ): OrderInterface {
        $storeId = (int)$order->getStore()->getId();

        try {
            if ($this->configProvider->isEnabled($storeId)
                && $this->configProvider->isRefundAllowed($storeId)
            ) {
                $this->refundGiftCardAccounts($order, $creditmemo);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }

        return $order;
    }

    /**
     * @param OrderInterface $order
     * @param CreditmemoInterface|Creditmemo $creditmemo
     *
     * @return void
     */
    private function refundGiftCardAccounts(OrderInterface $order, CreditmemoInterface $creditmemo): void
    {
        $gCardMemo = $this->gCardExtensionResolver->resolve($creditmemo);
        if (!$gCardMemo || $gCardMemo->getBaseGiftAmount() < .0) {
            return;
        }
        $this->giftCardRefundApplier->apply($order, $gCardMemo->getBaseGiftAmount());
    }
}
