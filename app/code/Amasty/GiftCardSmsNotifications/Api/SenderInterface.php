<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Gift Card Sms Notifications for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\GiftCardSmsNotifications\Api;

interface SenderInterface
{
    public function isNeedSend(string $notificationType, int $storeId): bool;

    public function send(string $recipientPhone, string $message, string $dltid): void;
}
