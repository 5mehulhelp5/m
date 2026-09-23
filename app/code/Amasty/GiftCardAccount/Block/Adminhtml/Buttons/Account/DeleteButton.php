<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Gift Card Account by Amasty (System)
 */

namespace Amasty\GiftCardAccount\Block\Adminhtml\Buttons\Account;

use Amasty\GiftCard\Block\Adminhtml\Buttons\AbstractDeleteButton;
use Amasty\GiftCardAccount\Api\Data\GiftCardAccountInterface;

class DeleteButton extends AbstractDeleteButton
{
    /**
     * @return string
     */
    protected function getIdField(): string
    {
        return GiftCardAccountInterface::ACCOUNT_ID;
    }
}
