<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Api;

interface GiftCardEmailSenderInterface
{
    /**
     * @param int $accountId
     * @param int $storeId
     * @param string|null $recipientEmail
     * @param string|null $recipientName
     *
     * @return bool
     */
    public function send(
        int $accountId,
        int $storeId,
        ?string $recipientEmail = null,
        ?string $recipientName = null
    ): bool;
}
