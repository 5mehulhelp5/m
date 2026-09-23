<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card for Magento 2
 */

namespace Amasty\GiftCard\Api\Data;

interface GiftCardEmailInterface
{
    public const GIFT_CODE = 'gift_code';
    public const RECIPIENT_NAME = 'recipient_name';
    public const RECIPIENT_EMAIL = 'recipient_email';
    public const SENDER_NAME = 'sender_name';
    public const SENDER_EMAIL = 'sender_email';
    public const SENDER_MESSAGE = 'sender_message';
    public const BALANCE = 'balance';
    public const EXPIRED_DATE = 'expired_date';
    public const IMAGE = 'image';
    public const EXPIRY_DAYS = 'expiry_days';
    public const IS_ALLOW_ASSIGN_TO_CUSTOMER = 'is_allow_assign_to_customer';
    public const IMAGE_ID = 'image_id';

    /**
     * @return string
     */
    public function getGiftCode(): string;

    /**
     * @param string $code
     *
     * @return GiftCardEmailInterface
     */
    public function setGiftCode(string $code): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getRecipientName();

    /**
     * @param string $name
     *
     * @return GiftCardEmailInterface
     */
    public function setRecipientName(string $name): GiftCardEmailInterface;

    /**
     * @return string
     */
    public function getRecipientEmail(): string;

    /**
     * @param string $email
     *
     * @return GiftCardEmailInterface
     */
    public function setRecipientEmail(string $email): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderName();

    /**
     * @param string $name
     *
     * @return GiftCardEmailInterface
     */
    public function setSenderName(string $name): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderEmail();

    /**
     * @param string $email
     *
     * @return GiftCardEmailInterface
     */
    public function setSenderEmail(string $email): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getSenderMessage();

    /**
     * @param string $message
     *
     * @return GiftCardEmailInterface
     */
    public function setSenderMessage(string $message): GiftCardEmailInterface;

    /**
     * @return string
     */
    public function getBalance(): string;

    /**
     * @param string $balance
     *
     * @return GiftCardEmailInterface
     */
    public function setBalance(string $balance): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getExpiredDate();

    /**
     * @param string|null $date
     *
     * @return GiftCardEmailInterface
     */
    public function setExpiredDate($date): GiftCardEmailInterface;

    /**
     * @return string|null
     */
    public function getImage();

    /**
     * @param string $image
     *
     * @return GiftCardEmailInterface
     */
    public function setImage(string $image): GiftCardEmailInterface;

    /**
     * @return int
     */
    public function getImageId(): int;

    /**
     * @param int $imageId
     *
     * @return GiftCardEmailInterface
     */
    public function setImageId(int $imageId): GiftCardEmailInterface;

    /**
     * @return int
     */
    public function getExpiryDays(): int;

    /**
     * @param int $days
     *
     * @return GiftCardEmailInterface
     */
    public function setExpiryDays(int $days): GiftCardEmailInterface;

    /**
     * @return bool
     */
    public function getIsAllowAssignToCustomer(): bool;

    /**
     * @param bool $allowAssign
     *
     * @return GiftCardEmailInterface
     */
    public function setIsAllowAssignToCustomer(bool $allowAssign): GiftCardEmailInterface;
}
