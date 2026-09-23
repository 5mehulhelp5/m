<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Model\Queue\Notifications\Dto;

class NotificationConfig
{
    /**
     * @var string|null
     */
    private ?string $event = null;

    /**
     * @var string|null
     */
    private ?string $code = null;

    /**
     * @var string|null
     */
    private ?string $recipientName = null;

    /**
     * @var string|null
     */
    private ?string $recipientEmail = null;

    /**
     * @var int|null
     */
    private ?int $storeId = null;

    /**
     * @return string|null
     */
    public function getEvent(): ?string
    {
        return $this->event;
    }

    /**
     * @param string|null $event
     * @return void
     */
    public function setEvent(?string $event): void
    {
        $this->event = $event;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return string|null
     */
    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    /**
     * @param string|null $recipientName
     * @return void
     */
    public function setRecipientName(?string $recipientName): void
    {
        $this->recipientName = $recipientName;
    }

    /**
     * @return string|null
     */
    public function getRecipientEmail(): ?string
    {
        return $this->recipientEmail;
    }

    /**
     * @param string|null $recipientEmail
     * @return void
     */
    public function setRecipientEmail(?string $recipientEmail): void
    {
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function setStoreId(?int $storeId): void
    {
        $this->storeId = $storeId;
    }
}
