<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Notification\Notifier;

use Amasty\GiftCard\Api\Data\GiftCardEmailInterfaceFactory;
use Amasty\GiftCard\Api\Data\GiftCardOptionInterface;
use Amasty\GiftCard\Model\ConfigProvider;
use Amasty\GiftCard\Utils\EmailSender;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Magento\Framework\Locale\CurrencyInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class GiftCardConfirmationNotifier implements GiftCardNotifierInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GiftCardEmailInterfaceFactory
     */
    private $cardEmailFactory;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var CurrencyInterface
     */
    private $localeCurrency;

    public function __construct(
        ConfigProvider $configProvider,
        GiftCardEmailInterfaceFactory $cardEmailFactory,
        EmailSender $emailSender,
        CurrencyInterface $localeCurrency
    ) {
        $this->configProvider = $configProvider;
        $this->cardEmailFactory = $cardEmailFactory;
        $this->emailSender = $emailSender;
        $this->localeCurrency = $localeCurrency;
    }

    public function notify(
        GiftCardAccountInterface $account,
        ?string $giftCardRecipientName = null,
        ?string $giftCardRecipientEmail = null,
        int $storeId = 0
    ): void {
        if (!($orderItem = $account->getOrderItem())) {
            return; //no recipient data for manual created accounts
        }

        $productOptions = $orderItem->getProductOptions();
        $storeId = $storeId ?: (int)$orderItem->getStoreId();

        if (!$this->needToSendEmail($orderItem, $storeId)) {
            return;
        }
        $recipientEmail = $productOptions[GiftCardOptionInterface::SENDER_EMAIL]
            ?? $orderItem->getOrder()->getCustomerEmail();
        $recipientName = $productOptions[GiftCardOptionInterface::SENDER_NAME]
            ?? $orderItem->getOrder()->getCustomerName();

        $cardEmail = $this->cardEmailFactory->create()
            ->setRecipientName(
                $giftCardRecipientName ?: $productOptions[GiftCardOptionInterface::RECIPIENT_NAME] ?? ''
            )->setSenderName($recipientName)
            ->setSenderMessage($productOptions[GiftCardOptionInterface::MESSAGE] ?? '')
            ->setBalance(
                $this->localeCurrency->getCurrency($orderItem->getStore($storeId)->getBaseCurrencyCode())
                    ->toCurrency($account->getInitialValue())
            )
            ->setIsAllowAssignToCustomer($this->configProvider->isAllowAssignToCustomer($storeId));

        $this->emailSender->sendEmail(
            [[$recipientEmail, $recipientName]],
            $this->configProvider->getEmailSender($storeId),
            $storeId,
            $this->configProvider->getSenderConfirmationTemplate($storeId),
            ['gcard_email' => $cardEmail]
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function needToSendEmail(OrderItemInterface $orderItem, int $storeId): bool
    {
        return $this->configProvider->isSendConfirmationToSender($storeId);
    }
}
