<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Queue\Notifications;

use Amasty\GiftCardAccount\Model\Queue\Notifications\Dto\NotificationConfig;
use Magento\Framework\MessageQueue\PublisherInterface;

class Publisher
{
    public const TOPIC_NAME = 'amasty_gift_card_account.send_notification';

    /**
     * @var PublisherInterface
     */
    private PublisherInterface $publisher;

    public function __construct(
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
    }

    public function publish(NotificationConfig $notification): void
    {
        if ($notification->getEvent() && $notification->getCode()) {
            $this->publisher->publish(self::TOPIC_NAME, $notification);
        }
    }
}
