<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Queue\Notifications;

use Amasty\GiftCardAccount\Api\GiftCardAccountRepositoryInterface;
use Amasty\GiftCardAccount\Model\Notification\NotifiersProvider;
use Amasty\GiftCardAccount\Model\Queue\Notifications\Dto\NotificationConfig;
use Psr\Log\LoggerInterface;

class Consumer
{
    /**
     * @var NotifiersProvider
     */
    private NotifiersProvider $notifiersProvider;

    /**
     * @var GiftCardAccountRepositoryInterface
     */
    private GiftCardAccountRepositoryInterface $accountRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        NotifiersProvider $notifiersProvider,
        GiftCardAccountRepositoryInterface $accountRepository,
        LoggerInterface $logger
    ) {
        $this->notifiersProvider = $notifiersProvider;
        $this->accountRepository = $accountRepository;
        $this->logger = $logger;
    }

    public function process(NotificationConfig $notification): void
    {
        try {
            $account = $this->accountRepository->getByCode($notification->getCode(), true);
            foreach ($this->notifiersProvider->get($notification->getEvent()) as $notifier) {
                $notifier->notify(
                    $account,
                    $notification->getRecipientName(),
                    $notification->getRecipientEmail(),
                    (int)$notification->getStoreId()
                );
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
