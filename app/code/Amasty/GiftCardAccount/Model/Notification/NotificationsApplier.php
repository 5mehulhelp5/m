<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Notification;

use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;
use Amasty\GiftCardAccount\Model\Queue\Notifications\Dto\NotificationConfigFactory;
use Amasty\GiftCardAccount\Model\Queue\Notifications\Publisher;
use Magento\Framework\App\ObjectManager;

class NotificationsApplier
{
    /**
     * @var Publisher
     */
    private Publisher $publisher;

    /**
     * @var NotificationConfigFactory
     */
    private NotificationConfigFactory $notificationConfigFactory;

    public function __construct(
        ?NotifiersProvider $notifiersProvider = null, //@deprecated
        ?Publisher $publisher = null,
        ?NotificationConfigFactory $notificationConfigFactory = null
    ) {
        $this->publisher = $publisher ?: ObjectManager::getInstance()->get(Publisher::class);
        $this->notificationConfigFactory = $notificationConfigFactory
            ?: ObjectManager::getInstance()->get(NotificationConfigFactory::class);
    }

    public function apply(
        string $event,
        GiftCardAccountInterface $account,
        ?string $giftCardRecipientName = null,
        ?string $giftCardRecipientEmail = null,
        int $storeId = 0
    ): void {
        $notification = $this->notificationConfigFactory->create();
        $notification->setEvent($event);
        $notification->setRecipientName($giftCardRecipientName);
        $notification->setRecipientEmail($giftCardRecipientEmail);
        $notification->setStoreId($storeId);
        if ($account->getCodeModel()) {
            $notification->setCode($account->getCodeModel()->getCode());
        }

        $this->publisher->publish($notification);
    }
}
