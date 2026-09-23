<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\GiftCard\Model\Config\Source\SendGiftCardEvent;
use Amasty\GiftCard\Model\ThirdParty\SubscriptionFunctionalityChecker;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * @var string
     */
    protected $pathPrefix = 'amgiftcard/';

    public const XPATH_ENABLED = 'general/active';
    public const ALLOWED_PRODUCT_TYPES = 'general/allowed_product_types';
    public const SHIPPING_PAID_ALLOWED = 'general/allow_to_paid_for_shipping';
    public const TAX_PAID_ALLOWED = 'general/allow_to_paid_for_tax';
    public const AUTO_CHANGE_ORDER_STATUS = 'general/auto_change_order_status';

    public const GIFT_CARD_FIELDS = 'display_options/fields';
    public const SHOW_OPTIONS_IN_CART_CHECKOUT = 'display_options/show_options_in_cart_checkout';
    public const GIFT_CARD_TIMEZONE = 'display_options/gift_card_timezone';
    public const ALLOW_USER_IMAGES = 'display_options/allow_user_images';
    public const IMAGE_UPLOAD_TOOLTIP = 'display_options/image_upload_tooltip';

    public const LIFETIME = 'card/lifetime';
    public const ALLOW_ASSIGN_TO_CUSTOMER = 'card/allow_assign_to_customer';
    public const ALLOW_USE_THEMSELVES = 'card/allow_use_themselves';
    public const NOTIFY_EXPIRES_DATE = 'card/notify_expires_date';
    public const NOTIFY_EXPIRES_DATE_DAYS = 'card/notify_expires_date_days';
    public const NOTIFY_BALANCE_UPDATE = 'card/notify_balance_update';

    public const EMAIL_SENDER = 'email/email_sender';
    public const EMAIL_TEMPLATE = 'email/email_template';
    public const EMAIL_RECIPIENTS = 'email/email_recipients';
    public const EMAIL_EXPIRATION_TEMPLATE = 'email/email_expiration_template';
    public const EMAIL_BALANCE_TEMPLATE = 'email/email_balance_change_template';
    public const SEND_CONFIRMATION_TO_SENDER = 'email/send_confirmation_to_sender';
    public const EMAIL_SENDER_CONFIRMATION_TEMPLATE = 'email/email_sender_confirmation_template';
    public const ATTACH_PDF_GIFT_CARD = 'email/attach_pdf_gift_card';
    public const ATTACH_PDF_GIFT_CARD_NAME = 'email/attach_pdf_gift_card_name';
    public const SEND_GIFT_CARD_EVENT = 'email/send_gift_card_event';

    /**
     * @var SubscriptionFunctionalityChecker
     */
    private SubscriptionFunctionalityChecker $checker;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ?SubscriptionFunctionalityChecker $checker = null
    ) {
        parent::__construct($scopeConfig);
        $this->checker = $checker ?? ObjectManager::getInstance()->get(SubscriptionFunctionalityChecker::class);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        return $this->isSetFlag(self::XPATH_ENABLED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getAllowedProductTypes($storeId = null): array
    {
        $allowedTypes = [];

        if ($value = $this->getValue(self::ALLOWED_PRODUCT_TYPES, $storeId)) {
            $allowedTypes = explode(',', $value);
        }

        return $allowedTypes;
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShippingPaidAllowed($storeId = null): bool
    {
        return $this->isSetFlag(self::SHIPPING_PAID_ALLOWED, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isTaxPaidAllowed($storeId = null): bool
    {
        return $this->isSetFlag(self::TAX_PAID_ALLOWED, $storeId);
    }

    /**
     * @deprecated settings must be checked from the modules that added it
     */
    public function isExtraFeePaidAllowed($storeId = null): bool
    {
        return $this->isSetFlag('general/allow_to_paid_for_amasty_extra_fee', $storeId);
    }

    public function isAutoChangeOrderStatusAllowed(): bool
    {
        return $this->isSetFlag(self::AUTO_CHANGE_ORDER_STATUS);
    }

    /**
     * @deprecated settings must be checked from the modules that added it
     */
    public function isGiftWrapPaidAllowed($storeId = null): bool
    {
        return $this->isSetFlag('general/allow_to_paid_for_gift_wrap', $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getGiftCardFields($storeId = null): array
    {
        $fieldsArray = [];

        if ($fields = $this->getValue(self::GIFT_CARD_FIELDS, $storeId)) {
            $fieldsArray = explode(',', $fields);
        }

        return $fieldsArray;
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isShowOptionsInCartAndCheckout($storeId = null): bool
    {
        return $this->isSetFlag(self::SHOW_OPTIONS_IN_CART_CHECKOUT, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getGiftCardTimezone($storeId = null): array
    {
        $timezonesArray = [];

        if ($timezones = $this->getValue(self::GIFT_CARD_TIMEZONE, $storeId)) {
            $timezonesArray = explode(',', $timezones);
        }

        return $timezonesArray;
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAllowUserImages($storeId = null): bool
    {
        return $this->isSetFlag(self::ALLOW_USER_IMAGES, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string|null
     */
    public function getImageUploadTooltip($storeId = null): string
    {
        return (string)$this->getValue(self::IMAGE_UPLOAD_TOOLTIP, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getLifetime($storeId = null): int
    {
        return (int)$this->getValue(self::LIFETIME, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAllowAssignToCustomer($storeId = null): bool
    {
        return $this->isSetFlag(self::ALLOW_ASSIGN_TO_CUSTOMER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAllowUseThemselves($storeId = null): bool
    {
        return $this->isSetFlag(self::ALLOW_USE_THEMSELVES, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isNotifyExpiresDate($storeId = null): bool
    {
        return $this->isSetFlag(self::NOTIFY_EXPIRES_DATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return int
     */
    public function getNotifyExpiresDateDays($storeId = null): int
    {
        return (int)$this->getValue(self::NOTIFY_EXPIRES_DATE_DAYS, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isNotifyBalanceChange($storeId = null): bool
    {
        return $this->isSetFlag(self::NOTIFY_BALANCE_UPDATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getEmailSender($storeId = null): string
    {
        return (string)$this->getValue(self::EMAIL_SENDER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getEmailTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::EMAIL_TEMPLATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getEmailRecipients($storeId = null): array
    {
        $recipientsArray = [];

        if ($recipients = $this->getValue(self::EMAIL_RECIPIENTS, $storeId)) {
            $recipientsArray = array_filter(array_map('trim', preg_split('/\n|\r\n?/', $recipients)));
        }

        return $recipientsArray;
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getEmailExpirationTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::EMAIL_EXPIRATION_TEMPLATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getEmailBalanceTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::EMAIL_BALANCE_TEMPLATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isSendConfirmationToSender($storeId = null): bool
    {
        return $this->isSetFlag(self::SEND_CONFIRMATION_TO_SENDER, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return string
     */
    public function getSenderConfirmationTemplate($storeId = null): string
    {
        return (string)$this->getValue(self::EMAIL_SENDER_CONFIRMATION_TEMPLATE, $storeId);
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isAttachPdfGiftCard($storeId = null): bool
    {
        return $this->isSetFlag(self::ATTACH_PDF_GIFT_CARD, $storeId);
    }

    public function getGiftCardPdfName(?int $storeId = null): string
    {
        return (string)$this->getValue(self::ATTACH_PDF_GIFT_CARD_NAME, $storeId);
    }

    public function getSendGiftCardEvent(?int $storeId = null): int
    {
        if (!$this->checker->isEnabled()) {
            return SendGiftCardEvent::INVOICED;
        }

        return (int)$this->getValue(self::SEND_GIFT_CARD_EVENT, $storeId);
    }
}
