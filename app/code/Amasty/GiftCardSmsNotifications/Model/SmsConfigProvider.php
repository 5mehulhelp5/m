<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Gift Card Sms Notifications for Magento 2 (System)
*/
declare(strict_types=1);

namespace Amasty\GiftCardSmsNotifications\Model;

use Amasty\GiftCard\Model\ConfigProvider as GiftCardConfigProvider;

class SmsConfigProvider extends GiftCardConfigProvider
{
    public const SMS_NOTIFICATION_ENABLE = 'sms_notification/enable';

    public const SMS_RECIPIENT_NOTIFICATION_ENABLE = 'recipient_notification/enable';
    public const SMS_RECIPIENT_NOTIFICATION_TEMPLATE = 'recipient_notification/template';
    public const SMS_RECIPIENT_NOTIFICATION_DLTID = 'recipient_notification/dltid';

    public const SMS_EXPIRY_NOTIFICATION_ENABLE = 'expiry_notification/enable';
    public const SMS_EXPIRY_NOTIFICATION_TEMPLATE = 'expiry_notification/template';
    public const SMS_EXPIRES_DATE_DAYS = 'expiry_notification/notify_expires_date_days';
    public const SMS_EXPIRY_NOTIFICATION_DLTID = 'expiry_notification/dltid';

    public const SMS_CHANGE_BALANCE_NOTIFICATION_ENABLE = 'change_balance_notification/enable';
    public const SMS_CHANGE_BALANCE_NOTIFICATION_TEMPLATE = 'change_balance_notification/template';
    public const SMS_CHANGE_BALANCE_NOTIFICATION_DLTID = 'change_balance_notification/dltid';

    public function isSmsNotify($storeId = null): bool
    {
        return $this->isSetFlag(self::SMS_NOTIFICATION_ENABLE, $storeId);
    }

    public function isSmsNotifyByType(string $notificationType, $storeId = null): bool
    {
        return $this->isSetFlag($notificationType, $storeId);
    }

    public function getSmsRecipientNotificationTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_RECIPIENT_NOTIFICATION_TEMPLATE, $storeId);
    }

    public function getSmsRecipientNotificationDltid($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_RECIPIENT_NOTIFICATION_DLTID, $storeId);
    }

    public function getSmsExpiryNotificationTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_EXPIRY_NOTIFICATION_TEMPLATE, $storeId);
    }

    public function getSmsNotifyExpiresDateDays($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_EXPIRES_DATE_DAYS, $storeId);
    }

    public function getSmsExpiryNotificationDltid($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_EXPIRY_NOTIFICATION_DLTID, $storeId);
    }

    public function getSmsChangeBalanceNotificationTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_CHANGE_BALANCE_NOTIFICATION_TEMPLATE, $storeId);
    }

    public function getSmsChangeBalanceNotificationDltid($storeId = null): string
    {
        return (string)$this->getValue(self::SMS_CHANGE_BALANCE_NOTIFICATION_DLTID, $storeId);
    }
}
